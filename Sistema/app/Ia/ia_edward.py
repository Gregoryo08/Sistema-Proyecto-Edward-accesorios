import numpy as np
import skfuzzy as fuzzy
from skfuzzy import control as ctrl
from flask import Flask, request, jsonify
from flask_cors import CORS

app = Flask(__name__)
CORS(app)


ingreso = ctrl.Antecedent(np.arange(0, 1201, 1), 'ingreso')
historial = ctrl.Antecedent(np.arange(0, 11, 1), 'historial')
estabilidad = ctrl.Antecedent(np.arange(0, 11, 1), 'estabilidad')

confiabilidad = ctrl.Consequent(np.arange(0, 101, 1), 'confiabilidad')
cuotas = ctrl.Consequent(np.arange(1, 13, 1), 'cuotas')

ingreso.automf(3, names=['bajo', 'medio', 'alto'])
historial.automf(3, names=['malo', 'regular', 'bueno'])
estabilidad.automf(3, names=['debil', 'estable', 'fuerte'])

confiabilidad['baja'] = fuzzy.trimf(confiabilidad.universe, [0, 0, 45])
confiabilidad['media'] = fuzzy.trimf(confiabilidad.universe, [35, 55, 75])
confiabilidad['alta'] = fuzzy.trimf(confiabilidad.universe, [60, 100, 100])

cuotas['pocas'] = fuzzy.trimf(cuotas.universe, [1, 1, 4])
cuotas['promedio'] = fuzzy.trimf(cuotas.universe, [3, 6, 9])
cuotas['muchas'] = fuzzy.trimf(cuotas.universe, [7, 12, 12])

regla1 = ctrl.Rule(ingreso['alto'] & estabilidad['fuerte'], [confiabilidad['alta'], cuotas['muchas']])
regla2 = ctrl.Rule(ingreso['medio'] | estabilidad['estable'], [confiabilidad['media'], cuotas['promedio']])
regla3 = ctrl.Rule(ingreso['bajo'] | estabilidad['debil'] | historial['malo'], [confiabilidad['baja'], cuotas['pocas']])
regla4 = ctrl.Rule(historial['bueno'], [confiabilidad['alta'], cuotas['muchas']])
regla5 = ctrl.Rule(ingreso['alto'] & historial['bueno'], [confiabilidad['alta'], cuotas['muchas']])

sistema_ia = ctrl.ControlSystem([regla1, regla2, regla3, regla4, regla5])
simulador = ctrl.ControlSystemSimulation(sistema_ia)

@app.route('/evaluar', methods=['POST'])
def evaluar_cliente():
    datos = request.json
    try:
      
        val_ingreso = max(0, min(1200, float(datos.get('ingreso', 0))))
        val_historial = max(0, min(10, float(datos.get('historial', 5))))
        
        puntos = 0
        residencia = datos.get('tipo_residencia', 'Familiar')
        profesion = datos.get('profesion', 'Empleado')
        cargas = int(datos.get('carga_familiar', 0))

        if residencia == 'Propia': puntos += 4
        elif residencia == 'Familiar': puntos += 3
        elif residencia == 'Alquilada': puntos += 1

        if profesion == 'Empleado': puntos += 4
        elif profesion == 'Independiente' or profesion == 'Pensionado': puntos += 3
        elif 'Estudiante' in profesion: puntos += 2
        
        if cargas > 2: puntos -= 1
        
        val_estabilidad = max(0, min(10, puntos))

        simulador.input['ingreso'] = val_ingreso
        simulador.input['historial'] = val_historial
        simulador.input['estabilidad'] = val_estabilidad
        simulador.compute()
        
        score = float(simulador.output['confiabilidad'])
        num_cuotas = float(simulador.output['cuotas'])
        
        nivel = "Bajo"
        if score >= 70: nivel = "Alto"
        elif 40 <= score < 70: nivel = "Medio"

        return jsonify({
            "puntaje_confianza": round(score, 2),
            "nivel_riesgo": nivel,
            "cuotas_recomendadas": int(round(num_cuotas)),
            "aprobado": "SI" if score >= 42 else "NO"
        })
    except Exception as e:
        return jsonify({"error": str(e)}), 400

if __name__ == '__main__':
    app.run(port=5000)
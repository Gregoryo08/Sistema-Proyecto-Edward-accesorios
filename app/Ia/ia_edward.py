import numpy as np
import skfuzzy as fuzzy
from skfuzzy import control as ctrl
from flask import Flask, request, jsonify
from flask_cors import CORS
import mysql.connector

app = Flask(__name__)
CORS(app)

ingreso = ctrl.Antecedent(np.arange(0, 1001, 1), 'ingreso')
historial = ctrl.Antecedent(np.arange(0, 101, 1), 'historial')

confiabilidad = ctrl.Consequent(np.arange(0, 101, 1), 'confiabilidad')
cuotas = ctrl.Consequent(np.arange(1, 13, 1), 'cuotas')

ingreso['bajo'] = fuzzy.trapmf(ingreso.universe, [0, 0, 150, 250])
ingreso['medio'] = fuzzy.trimf(ingreso.universe, [200, 400, 600])
ingreso['alto'] = fuzzy.trapmf(ingreso.universe, [500, 700, 1000, 1000])

historial.automf(3, names=['malo', 'regular', 'bueno'])

confiabilidad['baja'] = fuzzy.trimf(confiabilidad.universe, [0, 0, 45])
confiabilidad['media'] = fuzzy.trimf(confiabilidad.universe, [35, 55, 75])
confiabilidad['alta'] = fuzzy.trimf(confiabilidad.universe, [60, 100, 100])

cuotas['pocas'] = fuzzy.trimf(cuotas.universe, [1, 1, 4])
cuotas['promedio'] = fuzzy.trimf(cuotas.universe, [3, 6, 9])
cuotas['muchas'] = fuzzy.trimf(cuotas.universe, [7, 12, 12])

regla1 = ctrl.Rule(ingreso['alto'] & historial['bueno'], [confiabilidad['alta'], cuotas['muchas']])
regla2 = ctrl.Rule(ingreso['medio'] | historial['regular'], [confiabilidad['media'], cuotas['promedio']])
regla3 = ctrl.Rule(ingreso['bajo'] | historial['malo'], [confiabilidad['baja'], cuotas['pocas']])

sistema_ia = ctrl.ControlSystem([regla1, regla2, regla3])
simulador = ctrl.ControlSystemSimulation(sistema_ia)

@app.route('/evaluar', methods=['POST'])
def evaluar_cliente():
    datos = request.json
    cedula = datos.get('cedula')
    
    try:
        conn = mysql.connector.connect(
            host="127.0.0.1",
            port=3306,
            user="root",
            password="",
            database="sistema_edward"
        )
        cursor = conn.cursor(dictionary=True)
        cursor.execute("SELECT ingresos_mensuales, score_credito FROM perfiles_financiamiento WHERE cedula_persona = %s", (cedula,))
        perfil = cursor.fetchone()
        cursor.close()
        conn.close()

        if not perfil:
            return jsonify({"error": "No se encontró perfil financiero"}), 404

        simulador.input['ingreso'] = float(perfil['ingresos_mensuales'])
        simulador.input['historial'] = float(perfil['score_credito']) * 10
        
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
# language: es
Característica: Acceso de clientes
  Como cliente registrado
  Quiero iniciar sesión en la tienda
  Para gestionar mis pedidos

  Escenario: Ver el formulario de acceso
    Dado que abro la página de acceso
    Entonces debería ver el formulario de login
    Y debería ver el campo de cédula

  Escenario: Completar las credenciales
    Dado que abro la página de acceso
    Cuando ingreso la cédula "V-12345678"
    Y ingreso la contraseña "claveDePrueba"
    Entonces el campo cédula debería contener "V-12345678"

# language: es
Característica: Catálogo de productos
  Como visitante de la tienda
  Quiero explorar los productos disponibles
  Para decidir qué comprar

  Escenario: Ver el catálogo sin iniciar sesión
    Dado que abro la página del catálogo
    Entonces debería ver la lista de productos
    Y debería ver el buscador de productos

  Escenario: Ver la ayuda del buscador
    Dado que abro la página del catálogo
    Entonces el buscador debería tener el texto de ayuda "Buscar productos..."

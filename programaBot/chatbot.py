import mysql.connector
from flask import Flask, jsonify, request
import datetime

# Variable globales
conversacionEstablecida = False # Variable global para controlar la conversación establecida
estadoConversacion = "inicio" #Variable global para controlar el estado


#*********************************Funciones******************* 
#****************** Conexion a la bbdd
# #Conectamos con la bbdd
def conectar():
    conexion = mysql.connector.connect(
        host="localhost",
        user="chatbot",
        password="",
        database="principal"
    )
    cursor = conexion.cursor()
    return conexion, cursor

#Desconectamos con la bbdd
def desconectar(conexion, cursor):
    # Confirmar los cambios y cerrar la conexión
    conexion.commit()
    cursor.close()
    conexion.close()

#***************Funciones con Facebook **********

def enviar(telefonoRecibe,respuesta):
    from heyoo import WhatsApp
    #TOKEN de acceso de facebook se obtiene en la página
    token=''

    #identificador del numero de telefono, se obtiene desde la pagina de facebook
    idNumeroTeléfono='120118994395988'
    
    #INICIALIZAMOS ENVIO DE MENSAJES
    mensajeWa=WhatsApp(token,idNumeroTeléfono)
    telefonoRecibe=telefonoRecibe.replace("521","52")
    
    #ENVIAMOS UN MENSAJE DE TEXTO
    mensajeWa.send_message(respuesta,telefonoRecibe)

#*****************Funciones inicio de conversacion**************

def establecerConversacion():
    global conversacionEstablecida
    global estadoConversacion
    conversacionEstablecida = True
    estadoConversacion = "inicio"
    print("Conversación establecida")

#***********************Fuciones normales
#listamos los dedidos del cliente actual y con fecha de hoy
def listarPedidoCliente(idCliente):
    
     # Conectar a la base de datos
    conexion, cursor = conectar()
    consulta = "SELECT nombre,cantidad FROM pedido WHERE idCliente = %s AND fecha = CURDATE()"
    valores = (idCliente,)  # Asegúrate de agregar una coma después de 'telefono' para crear una tupla

    # Ejecutar la consulta
    cursor.execute(consulta, valores)
    # Obtener el resultado de la consulta
    resultados = cursor.fetchall()
    productosPedidos = []
    for resultado in resultados:
        nombre = resultado[0]
        cantidad = resultado[1]
        productosPedidos.append((nombre, cantidad))
    # Cerrar el cursor y la conexión
    desconectar(conexion,cursor)
    return productosPedidos


#Buscamos el producto por su id, si no lo encuentra nos dará un -1
def buscarId(mensaje):
    producto=-1
    idProducto=mensaje.strip() #Eliminamos espacios en blancos
    
    for producto in listarProductos():
        if producto["idProducto"]==idProducto:
            producto=idProducto
            print("el id es: ", idProducto ,"y ", producto)
    return producto

#Insertamos el pedido con la fecha actual y el cliente y actualizamos la tabla de los productos disponibles
def insertarPedido(idProducto, idCliente):
   
    # Conectar a la base de datos
    conexion, cursor = conectar()

    # Obtener la fecha actual del sistema
    fechaActual = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")

    try:
        # Obtener el nombre y las unidades del producto a partir del ID
        consultaProducto = "SELECT nombre, unidad FROM producto WHERE idProducto = %s"
        valoresProducto = (idProducto,)
        cursor.execute(consultaProducto, valoresProducto)
        resultadoProducto = cursor.fetchone()

        if resultadoProducto:
            nombreProducto = resultadoProducto[0]
            unidadesProducto = resultadoProducto[1]

            # Verificar que haya unidades disponibles
            if unidadesProducto > 0:
                # Restar una unidad al producto
                unidadesActualizadas = unidadesProducto - 1

                # Actualizar las unidades del producto en la base de datos
                consultaActualizarProducto = "UPDATE producto SET unidad = %s WHERE idProducto = %s"
                valoresActualizarProducto = (unidadesActualizadas, idProducto)
                cursor.execute(consultaActualizarProducto, valoresActualizarProducto)

                # Insertar el pedido en la base de datos
                consultaInsertarPedido = "INSERT INTO pedido (idProducto, nombre, cantidad, fecha, hora, idCliente) VALUES (%s, %s, %s,%s, %s, %s)"
                horaActual = datetime.datetime.now().strftime("%H:%M:%S")

                valoresInsertarPedido = (idProducto, nombreProducto, 1, fechaActual,horaActual, idCliente)
                cursor.execute(consultaInsertarPedido, valoresInsertarPedido)
                
                # Confirmar los cambios en la base de datos
                conexion.commit()  

                respuesta = "El producto: "+(nombreProducto)+"\ncon el id: "+str(idProducto)+" fue insertado al pedido correctamente."
            else:
                respuesta = "No hay unidades disponibles para el producto seleccionado."
        else:
            respuesta = "No se encontró un producto con el ID proporcionado."

    except Exception as e:
        respuesta = "Error al insertar el pedido en la base de datos: " + str(e)

    # Cerrar la conexión a la base de datos
    desconectar(conexion, cursor)

    return respuesta

#Listamos los pruductos su id, nombre y unidades
def listarProductos():
    productos = []
    # Conectamos con la bbdd
    conexion, cursor = conectar()
    # Ejecutar consulta para obtener los datos de la tabla
    consulta = "SELECT idProducto, nombre, unidad FROM producto"
    cursor.execute(consulta)
    filas = cursor.fetchall()

    # Recorrer las filas y agregar los productos a la lista
    for fila in filas:
        idProducto = fila[0]
        nombre = fila[1]
        unidades = fila[2]
        if unidades > 0:
            producto = {"idProducto": idProducto, "nombre": nombre, "unidad": unidades}
            productos.append(producto)

    desconectar(conexion, cursor)
    return productos



#En esta funcion comprobamos que el cliente exista en la bbdd en caso de que no exista devolvera valores de falso y -1 al id de cliente
def comprobarCliente(telefono):
    comprobar = False
    # conectamos con la bbdd
    conexion, cursor = conectar()  
    consulta = "SELECT idCliente FROM cliente WHERE telefono = %s"
    valores = (telefono,)  # Asegúrate de agregar una coma después de 'telefono' para crear una tupla

    # Ejecutar la consulta
    cursor.execute(consulta, valores)
    # Obtener el resultado de la consulta
    resultado = cursor.fetchone()
    # Verificar si se encontró el cliente
    if resultado:
        idCliente = resultado[0]
        comprobar = True  # Establecer la variable 'comprobar' como verdadera si se encontró el cliente
    else:
        idCliente=-1

    # Cerrar el cursor y la conexión
    desconectar(conexion,cursor)
    return comprobar, idCliente

# ******************************Código de Recibir WhatsApp 

app = Flask(__name__)

#CUANDO RECIBAMOS LAS PETICIONES EN ESTA RUTA
@app.route("/webhook/", methods=["POST", "GET"])
def webhook_whatsapp():
    global estadoConversacion
    global conversacionEstablecida

    #SI HAY DATOS RECIBIDOS VIA GET
    if request.method == "GET":
        #SI EL TOKEN ES IGUAL AL QUE RECIBIMOS hay que poner en la página de facebook lo mismo en este caso "Hola"
        if request.args.get('hub.verify_token') == "Hola":
            #ESCRIBIMOS EN EL NAVEGADOR EL VALOR DEL RETO RECIBIDO DESDE FACEBOOK
            return request.args.get('hub.challenge')
        else:
            #SI NO SON IGUALES RETORNAMOS UN MENSAJE DE ERROR
          return "Error de autentificación..............."
    #RECIBIMOS TODOS LOS DATOS ENVIADO VIA JSON
    data=request.get_json()
    # Manejar el caso cuando no hay mensajes
    telefonoCliente = ''
    mensaje = ''

    #Extraemos los mensajes y el numero de telefono 
    if 'messages' in data['entry'][0]['changes'][0]['value']:
        telefonoCliente = data['entry'][0]['changes'][0]['value']['messages'][0].get('from', '')

        if 'text' in data['entry'][0]['changes'][0]['value']['messages'][0]:
            mensaje = data['entry'][0]['changes'][0]['value']['messages'][0]['text'].get('body', '')

    #si hay un mensaje y no esta vacio
    if mensaje is not None and mensaje != "":
        #Si la conversacion no esta establecida la establece
        if not conversacionEstablecida:
            establecerConversacion()

        #Vemos lo que nos envio
        print("Recibio el siguiente mensaje: "+ mensaje)
        #Comprobamos que el cliente exista
        clienteExiste,idCliente=comprobarCliente(telefonoCliente)

        if clienteExiste and idCliente!=-1:
            print("existe el cliente con numero de telefono "+telefonoCliente +"y con su id: ", idCliente)
            # Redirigir flujo de conversación
            if estadoConversacion == "inicio":
                if mensaje.lower() == "hola":
                    respuesta = "¡Hola! ¿En qué puedo ayudarte hoy?\nsi deseas añadir *pedido*,o realizar una *consulta* di las palabras en negrita"
                    estadoConversacion = "gestion"
                else:
                    respuesta = "Lo siento, no puedo entenderte. Por favor, comienza enviando 'hola'."
            
            #Flujo de conversacion estado de gestion se puede decir pedido o consulta
            elif estadoConversacion == "gestion":
                #Dice pedido
                if mensaje.lower() == "pedido":
                    respuesta = "Perfecto, vamos a añadir el pedido. Por favor, seleccione los id del producto, y cuando termine ponga *listo*.\n"
                    productos = listarProductos()
                    if productos:
                        respuesta += "Lista de productos:\n"
                        for producto in productos:
                            respuesta += f"- ID: {producto['idProducto']}, Nombre: {producto['nombre']}, Unidad: {producto['unidad']}\n"
                    else:
                        respuesta += "No hay productos disponibles en este momento."
                    estadoConversacion = "añadirPedido"
                #Dice consulta
                elif mensaje.lower() == "consulta":
                    respuesta="\nYo no puedo ayudarle con las consultas \nPero le dejo el telefono de contacto y email con otro vendedor que puede atender todas tus consultas: \n *634923712* \n *josevendedor22@gmail.com* \n¡Hasta luego, un placer!"
                    estadoConversacion="inicio"
                #No entiende lo que dice
                else:
                    respuesta = "Si quiere continuar puede eligir:\n*pedido:* ->Para hacer un pedido,\n*consulta:*-> le ofecemos nuestro atención al cliente."
                    estadoConversacion = "inicio"    

            #añadimos el pedido
            elif estadoConversacion == "añadirPedido":
                if mensaje.lower() == "listo":
                    # Obtener los pedidos del cliente
                    pedidos_cliente = listarPedidoCliente(idCliente)
                    
                    if pedidos_cliente:
                        respuesta = "Sus pedidos son:\n"
                        for pedido in pedidos_cliente:
                            nombre_producto = pedido[0]
                            cantidad = pedido[1]
                            respuesta += f"- {nombre_producto}: {cantidad}\n"
                        respuesta+="\nGracias por la visita :)"
                    else:
                        respuesta = "No se encontraron pedidos asociados a su cuenta."
                    estadoConversacion="inicio"    
                else:
                    idProducto=buscarId(mensaje)
                    if idProducto:
                        respuesta=insertarPedido(mensaje,idCliente)
                    else:
                        respuesta="No se encontro el producto con ese id: ", mensaje
            else:
                respuesta = "Lo siento, ha ocurrido un error. Reinicia la conversación enviando *hola*."
                estadoConversacion="inicio"
            
            enviar(telefonoCliente, respuesta)
        else: 
            print("No existe el cliente")
    else:
        print("No se recibieron mensajes")
    #RETORNAMOS EL STATUS EN UN JSON fue exitoso
    response = {'message': 'Respuesta recibida correctamente'}
    return jsonify(response)
    


#INICIAMSO FLASK
if __name__ == "__main__":
    # Establecer la conversación al iniciar el servidor
    establecerConversacion()

    # Iniciar el servidor Flask
    app.run(host='0.0.0.0', port=5000)
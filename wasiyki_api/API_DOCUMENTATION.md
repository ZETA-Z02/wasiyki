# Documentación de la API de Wasiyki

Esta documentación describe todas las rutas de la API de **Wasiyki** para realizar solicitudes HTTP desde el frontend (React o móvil).

---

## Configuración General

* **Base URL:** `http://localhost:8000/api` (o la URL de tu servidor local/producción con el prefijo `/api`).
* **Headers Obligatorios:**
  * `Accept: application/json`
  * `Content-Type: application/json`
* **Autenticación:** Las rutas protegidas requieren un token de portador (Bearer Token) obtenido en el Login o mediante OAuth Google.
  * `Authorization: Bearer <TOKEN_DE_SANCTUM>`

---

## 1. Módulo de Autenticación y Perfil

### `POST /login` (Público)
Inicia sesión de un arrendador utilizando sus credenciales tradicionales.

* **Cuerpo de la Solicitud (Request Body):**
  ```json
  {
    "email": "correo@ejemplo.com", // Requerido, formato email
    "password": "mi_contraseña_segura" // Requerido
  }
  ```
* **Respuestas:**
  * **`200 OK`** (Login exitoso):
    ```json
    {
      "token": "1|abcdef123456...",
      "arrendador": {
        "id": 1,
        "nombre": "Juan",
        "apellido": "Pérez",
        "telefono": "987654321",
        "email": "correo@ejemplo.com",
        "fecha_registro": "2026-07-15"
      }
    }
    ```
  * **`401 Unauthorized`** (Credenciales incorrectas):
    ```json
    {
      "message": "Credenciales incorrectas"
    }
    ```

---

### `GET /auth/google/redirect` (Público)
Redirige al flujo interactivo de OAuth2 de Google.

* **Respuestas:** Redirección HTTP (`302`) a las pantallas de consentimiento de Google.

---

### `GET /auth/google/callback` (Público)
Callback de Google que procesa la respuesta e inicia sesión o registra al usuario si es nuevo.

* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "token": "2|ghijk789...",
      "arrendador": {
        "id": 1,
        "nombre": "Juan",
        "apellido": "Pérez",
        "telefono": "987654321",
        "email": "correo@ejemplo.com",
        "fecha_registro": "2026-07-15"
      }
    }
    ```

---

### `POST /auth/google/pkce` (Público)
Intercambia el código de autorización obtenido en el frontend por un token de Sanctum para aplicaciones cliente (React, Móviles).

* **Cuerpo de la Solicitud (Request Body):**
  ```json
  {
    "code": "4/0AdQt8...", // Requerido, string del código de autorización
    "redirect_uri": "http://localhost:3000/auth/callback", // Requerido, URI registrada en la consola de Google
    "code_verifier": "verificador_pkce..." // Opcional, string
  }
  ```
* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "token": "3|lmnop456...",
      "arrendador": {
        "id": 1,
        "nombre": "Juan",
        "email": "correo@ejemplo.com"
      }
    }
    ```
  * **`401 Unauthorized`**:
    ```json
    {
      "message": "Error de autenticación federada con Google",
      "details": { ... } // Mensaje crudo devuelto por el API de Google
    }
    ```

---

### `GET /arrendador/me` (Protegido)
Obtiene los datos del arrendador actualmente autenticado.

* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "data": {
        "id": 1,
        "nombre": "Juan",
        "apellido": "Pérez",
        "telefono": "987654321",
        "email": "correo@ejemplo.com",
        "fecha_registro": "2026-07-15"
      }
    }
    ```

---

### `PUT /arrendador/update` (Protegido)
Actualiza los datos del arrendador autenticado.

* **Cuerpo de la Solicitud (Request Body):**
  ```json
  {
    "nombre": "Juan Carlos", // Opcional, string, máx 255
    "apellido": "Pérez Gómez", // Opcional, string, máx 255
    "telefono": "999888777", // Opcional, string, máx 20
    "email": "nuevo_correo@ejemplo.com", // Opcional, email, único (excepto él mismo)
    "password": "nueva_contraseña" // Opcional, string, mín 8
  }
  ```
* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "data": {
        "id": 1,
        "nombre": "Juan Carlos",
        "apellido": "Pérez Gómez",
        "telefono": "999888777",
        "email": "nuevo_correo@ejemplo.com",
        "fecha_registro": "2026-07-15"
      }
    }
    ```

---

### `POST /logout` (Protegido)
Revoca el token actual con el que se está haciendo la petición.

* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "message": "Sesión cerrada correctamente"
    }
    ```

---

## 2. Módulo de Habitaciones

### `GET /habitaciones` (Protegido)
Lista todas las habitaciones asociadas al arrendador autenticado.

* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "data": [
        {
          "id": 1,
          "piso": 1,
          "numero": "101",
          "descripcion": "Habitación con ventana a la calle",
          "precio": 450.00,
          "estado": "disponible"
        },
        {
          "id": 2,
          "piso": 2,
          "numero": "201",
          "descripcion": "Habitación pequeña con baño",
          "precio": 500.00,
          "estado": "ocupada"
        }
      ]
    }
    ```

---

### `GET /habitaciones/disponibles` (Protegido)
Obtiene únicamente las habitaciones cuyo estado sea `"disponible"`.

* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "data": [
        {
          "id": 1,
          "piso": 1,
          "numero": "101",
          "descripcion": "Habitación con ventana a la calle",
          "precio": 450.00,
          "estado": "disponible"
        }
      ]
    }
    ```

---

### `POST /habitaciones` (Protegido)
Crea una nueva habitación.

* **Cuerpo de la Solicitud (Request Body):**
  ```json
  {
    "piso": 1, // Requerido, entero
    "numero": "102", // Requerido, string, máx 50
    "descripcion": "Habitación estándar", // Opcional, string
    "precio": 400.00, // Requerido, numérico positivo
    "estado": "disponible" // Opcional, valores: "disponible", "ocupada", "mantenimiento". Por defecto: "disponible"
  }
  ```
* **Respuestas:**
  * **`201 Created`**:
    ```json
    {
      "data": {
        "id": 3,
        "piso": 1,
        "numero": "102",
        "descripcion": "Habitación estándar",
        "precio": 400.00,
        "estado": "disponible"
      }
    }
    ```

---

### `GET /habitaciones/{id}` (Protegido)
Obtiene el detalle de una habitación específica por su ID.

* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "data": {
        "id": 1,
        "piso": 1,
        "numero": "101",
        "descripcion": "Habitación con ventana a la calle",
        "precio": 450.00,
        "estado": "disponible"
      }
    }
    ```

---

### `PUT /habitaciones/{id}` (Protegido)
Actualiza los datos de una habitación.

* **Cuerpo de la Solicitud (Request Body):**
  ```json
  {
    "piso": 1, // Opcional, entero
    "numero": "101-Modificado", // Opcional, string, máx 50
    "descripcion": "Nueva descripción de la habitación", // Opcional, string
    "precio": 480.00, // Opcional, numérico positivo
    "estado": "mantenimiento" // Opcional, valores: "disponible", "ocupada", "mantenimiento"
  }
  ```
* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "data": {
        "id": 1,
        "piso": 1,
        "numero": "101-Modificado",
        "descripcion": "Nueva descripción de la habitación",
        "precio": 480.00,
        "estado": "mantenimiento"
      }
    }
    ```

---

### `DELETE /habitaciones/{id}` (Protegido)
Elimina de forma lógica (Soft Delete) una habitación.

* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "message": "Habitación eliminada correctamente"
    }
    ```

---

## 3. Módulo de Inquilinos

### `GET /inquilinos` (Protegido)
Lista todos los inquilinos asociados al arrendador.

* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "data": [
        {
          "id": 1,
          "nombre": "Carlos",
          "apellido": "Mendoza",
          "telefono": "987111222",
          "email": "carlos@ejemplo.com",
          "dni": "77665544",
          "fecha_nacimiento": "1993-04-12"
        }
      ]
    }
    ```

---

### `POST /inquilinos` (Protegido)
Registra un nuevo inquilino.

* **Cuerpo de la Solicitud (Request Body):**
  ```json
  {
    "nombre": "Carlos", // Requerido, string, máx 255
    "apellido": "Mendoza", // Requerido, string, máx 255
    "telefono": "987111222", // Opcional, string, máx 20
    "email": "carlos@ejemplo.com", // Opcional, email, máx 255
    "dni": "77665544", // Requerido, string, único en inquilinos
    "fecha_nacimiento": "1993-04-12" // Opcional, fecha
  }
  ```
* **Respuestas:**
  * **`201 Created`**:
    ```json
    {
      "data": {
        "id": 1,
        "nombre": "Carlos",
        "apellido": "Mendoza",
        "telefono": "987111222",
        "email": "carlos@ejemplo.com",
        "dni": "77665544",
        "fecha_nacimiento": "1993-04-12"
      }
    }
    ```

---

### `GET /inquilinos/{id}` (Protegido)
Obtiene el detalle de un inquilino específico.

* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "data": {
        "id": 1,
        "nombre": "Carlos",
        "apellido": "Mendoza",
        "telefono": "987111222",
        "email": "carlos@ejemplo.com",
        "dni": "77665544",
        "fecha_nacimiento": "1993-04-12"
      }
    }
    ```

---

### `PUT /inquilinos/{id}` (Protegido)
Actualiza los datos del inquilino.

* **Cuerpo de la Solicitud (Request Body):**
  ```json
  {
    "nombre": "Carlos Alberto", // Opcional, string
    "apellido": "Mendoza Rivas", // Opcional, string
    "telefono": "987111223", // Opcional, string
    "email": "carlos.nuevo@ejemplo.com", // Opcional, email
    "dni": "77665544", // Opcional, string, único (excepto él mismo)
    "fecha_nacimiento": "1993-04-12" // Opcional, fecha
  }
  ```
* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "data": {
        "id": 1,
        "nombre": "Carlos Alberto",
        "apellido": "Mendoza Rivas",
        "telefono": "987111223",
        "email": "carlos.nuevo@ejemplo.com",
        "dni": "77665544",
        "fecha_nacimiento": "1993-04-12"
      }
    }
    ```

---

### `DELETE /inquilinos/{id}` (Protegido)
Elimina lógicamente a un inquilino.

* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "message": "Inquilino eliminado correctamente"
    }
    ```

---

## 4. Módulo de Contratos

### `GET /contratos` (Protegido)
Lista todos los contratos cargando los datos completos del Inquilino y de la Habitación.

* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "data": [
        {
          "id": 1,
          "canon_mensual": 500.00,
          "estado_contrato": "activo",
          "tipo_contrato": "fijo",
          "fecha_inicio": "2026-07-01",
          "fecha_fin": "2027-07-01",
          "inquilino": {
            "id": 1,
            "nombre": "Carlos",
            "apellido": "Mendoza",
            "telefono": "987111222",
            "email": "carlos@ejemplo.com",
            "dni": "77665544",
            "fecha_nacimiento": "1993-04-12"
          },
          "habitacion": {
            "id": 2,
            "piso": 2,
            "numero": "201",
            "descripcion": "Habitación pequeña con baño",
            "precio": 500.00,
            "estado": "ocupada"
          },
          "inquilino_id": 1,
          "habitacion_id": 2
        }
      ]
    }
    ```

---

### `POST /contratos` (Protegido)
Crea un nuevo contrato.
*Nota: Este proceso cambia automáticamente el estado de la habitación a `"ocupada"`.*

* **Cuerpo de la Solicitud (Request Body):**
  ```json
  {
    "inquilino_id": 1, // Requerido, debe existir en inquilinos
    "habitacion_id": 2, // Requerido, debe existir en habitaciones y estar disponible
    "canon_mensual": 500.00, // Requerido, numérico positivo
    "estado_contrato": "activo", // Opcional, valores: "activo", "finalizado", "con_deuda". Por defecto: "activo"
    "tipo_contrato": "fijo", // Requerido, valores: "fijo", "indefinido"
    "fecha_inicio": "2026-07-01", // Requerido, fecha
    "fecha_fin": "2027-07-01" // Requerido si tipo_contrato es "fijo". Debe ser posterior o igual a fecha_inicio
  }
  ```
* **Respuestas:**
  * **`201 Created`**:
    ```json
    {
      "data": {
        "id": 1,
        "canon_mensual": 500.00,
        "estado_contrato": "activo",
        "tipo_contrato": "fijo",
        "fecha_inicio": "2026-07-01",
        "fecha_fin": "2027-07-01",
        "inquilino": { ... },
        "habitacion": { ... },
        "inquilino_id": 1,
        "habitacion_id": 2
      }
    }
    ```
  * **`422 Unprocessable Entity`** (La habitación no estaba disponible):
    ```json
    {
      "message": "La habitación seleccionada no está disponible."
    }
    ```

---

### `GET /contratos/{id}` (Protegido)
Obtiene el detalle de un contrato específico con sus relaciones.

* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "data": {
        "id": 1,
        "canon_mensual": 500.00,
        ...
      }
    }
    ```

---

### `PUT /contratos/{id}` (Protegido)
Actualiza un contrato.
*Nota: Si el `estado_contrato` cambia a `"finalizado"`, la habitación asociada cambia automáticamente su estado a `"disponible"`.*

* **Cuerpo de la Solicitud (Request Body):**
  ```json
  {
    "inquilino_id": 1, // Opcional, debe existir en inquilinos
    "habitacion_id": 2, // Opcional, debe existir en habitaciones
    "canon_mensual": 520.00, // Opcional, numérico
    "estado_contrato": "finalizado", // Opcional, valores: "activo", "finalizado", "con_deuda"
    "tipo_contrato": "fijo", // Opcional
    "fecha_inicio": "2026-07-01", // Opcional
    "fecha_fin": "2027-07-01" // Opcional
  }
  ```
* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "data": {
        "id": 1,
        "estado_contrato": "finalizado",
        ...
      }
    }
    ```

---

### `POST /contratos/{id}/terminar` (Protegido)
Finaliza un contrato de forma inmediata (pone el estado del contrato como `"finalizado"`, la fecha de fin al día de hoy, y libera la habitación poniéndola como `"disponible"`).

* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "message": "Contrato finalizado y habitación liberada correctamente."
    }
    ```

---

### `DELETE /contratos/{id}` (Protegido)
Elimina lógicamente el contrato. Libera la habitación a `"disponible"` antes de eliminar el contrato.

* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "message": "Contrato finalizado/eliminado correctamente"
    }
    ```

---

## 5. Módulo de Pagos

### `GET /pagos` (Protegido)
Lista todos los pagos registrados, incluyendo la información de la relación contrato e inquilino.

* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "data": [
        {
          "id": 1,
          "monto": 500.00,
          "fecha_pago": "2026-07-15",
          "periodo": "Julio 2026",
          "metodo_pago": "yape",
          "numero_comprobante": "CP-ABCDEFGH",
          "observaciones": "Pago puntual",
          "contrato": {
            "id": 1,
            "canon_mensual": 500.00,
            "estado_contrato": "activo"
            // ... datos simplificados del contrato
          },
          "contrato_id": 1
        }
      ]
    }
    ```

---

### `POST /pagos` (Protegido)
Registra un nuevo pago para un contrato.
*Notas:*
* *Si no se ingresa un `numero_comprobante`, el sistema autogenera uno con el prefijo `CP-`.*
* *Si el contrato estaba con estado `"con_deuda"`, registrar un pago cambiará automáticamente el contrato a `"activo"`.*

* **Cuerpo de la Solicitud (Request Body):**
  ```json
  {
    "contrato_id": 1, // Requerido, debe existir en contratos
    "monto": 500.00, // Requerido, mínimo 0.01
    "fecha_pago": "2026-07-15", // Requerido, fecha
    "periodo": "Julio 2026", // Requerido, string (Ejemplo: "Julio 2026")
    "metodo_pago": "yape", // Requerido, valores: "efectivo", "transferencia", "yape", "plin", "otro"
    "numero_comprobante": "CP-ABCDEFGH", // Opcional, string, máx 100
    "observaciones": "Sin observaciones" // Opcional, texto libre
  }
  ```
* **Respuestas:**
  * **`201 Created`**:
    ```json
    {
      "data": {
        "id": 1,
        "monto": 500.00,
        "fecha_pago": "2026-07-15",
        "periodo": "Julio 2026",
        "metodo_pago": "yape",
        "numero_comprobante": "CP-ABCDEFGH",
        "observaciones": "Sin observaciones",
        "contrato_id": 1
      }
    }
    ```

---

### `GET /pagos/{id}` (Protegido)
Obtiene los detalles de un pago específico.

* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "data": {
        "id": 1,
        "monto": 500.00,
        "fecha_pago": "2026-07-15",
        "periodo": "Julio 2026",
        "metodo_pago": "yape",
        "numero_comprobante": "CP-ABCDEFGH",
        "observaciones": "Sin observaciones",
        "contrato_id": 1
      }
    }
    ```

---

### `PUT /pagos/{id}` (Protegido)
Actualiza los datos de un pago registrado.

* **Cuerpo de la Solicitud (Request Body):**
  ```json
  {
    "contrato_id": 1, // Opcional
    "monto": 500.00, // Opcional
    "fecha_pago": "2026-07-15", // Opcional
    "periodo": "Julio 2026", // Opcional
    "metodo_pago": "transferencia", // Opcional
    "numero_comprobante": "CP-NUEVOCODE", // Opcional
    "observaciones": "Corrección de método" // Opcional
  }
  ```
* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "data": {
        "id": 1,
        "monto": 500.00,
        "fecha_pago": "2026-07-15",
        "periodo": "Julio 2026",
        "metodo_pago": "transferencia",
        "numero_comprobante": "CP-NUEVOCODE",
        "observaciones": "Corrección de método",
        "contrato_id": 1
      }
    }
    ```

---

### `DELETE /pagos/{id}` (Protegido)
Elimina físicamente el pago.

* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "message": "Pago eliminado correctamente"
    }
    ```

---

### `GET /pagos/{id}/comprobante` (Protegido)
Genera y descarga un comprobante de pago en formato PDF.

* **Respuestas:**
  * **`200 OK`**: Descarga directa de archivo PDF (`comprobante_<numero_comprobante>.pdf`).

---

## 6. Módulo Dashboard

### `GET /dashboard` (Protegido)
Obtiene métricas resumidas de habitaciones, finanzas, alertas y últimos pagos del arrendador autenticado. Formateado para consumirse directamente en el componente de React.

* **Respuestas:**
  * **`200 OK`**:
    ```json
    {
      "habitacionesOcupadas": 8,
      "habitacionesTotales": 10,
      "ingresosMes": 2500.0,
      "disponibles": [
        "Hab. 201",
        "Hab. 203"
      ],
      "alertas": [
        {
          "id": "deuda-2",
          "mensaje": "María Gómez (Hab. 102)",
          "tipo": "atraso",
          "monto": "S/ 450"
        },
        {
          "id": "proximo-1",
          "mensaje": "Juan Pérez (Hab. 101)",
          "tipo": "proximo",
          "fecha": "Mañana"
        }
      ],
      "ultimosPagos": [
        {
          "id": 2,
          "inquilino": "Juan Pérez",
          "habitacion": "Hab. 101",
          "monto": 500.0,
          "fecha": "15 Jul, 2026",
          "metodo": "Transferencia"
        }
      ]
    }
    ```

---

## 7. Módulo de Recordatorios

### `POST /contratos/{contrato_id}/recordatorio` (Protegido)
Envía manualmente una notificación o recordatorio de pago al inquilino del contrato a través de un canal especificado (WhatsApp o Email).

* **Cuerpo de la Solicitud (Request Body):**
  ```json
  {
    "canal": "whatsapp", // Requerido, valores permitidos: "whatsapp", "email"
    "mensaje": "Estimado Carlos, le recordamos que el pago de su mensualidad de 500 soles vence pronto." // Requerido, string con el mensaje a enviar
  }
  ```
* **Respuestas:**
  * **`200 OK`** (Envío exitoso):
    ```json
    {
      "message": "Recordatorio enviado exitosamente por whatsapp"
    }
    ```
  * **`400 Bad Request`** (Fallo en el envío por falta de datos de contacto):
    ```json
    {
      "message": "No se pudo enviar el recordatorio. Verifique los datos del inquilino."
    }
    ```

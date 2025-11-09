# 🩺 ENFERMERÍA – Backend API

## 📋 Descripción

Este proyecto es una **API backend desarrollada con Symfony** enfocada en la gestión de **enfermeros y pacientes**.  
Actualmente se está trabajando únicamente en el backend (sin frontend).  

La API permite realizar operaciones **CRUD** sobre la entidad **"Enfermeros"**, además de validar el **login mediante usuario y contraseña**.

El desarrollo se ha realizado en dos fases principales:

1. **Primera fase**: CRUD local utilizando archivos **JSON** para simular la persistencia de datos.  
2. **Segunda fase (actual)**: CRUD completo conectado a una **base de datos real**, con **integración continua (CI)** y **pruebas unitarias automáticas**.

---

## ⚙️ Instalación

Para instalar y ejecutar el proyecto en tu entorno local, sigue estos pasos:

1. **Clona el repositorio:**

   ```bash
   git clone https://github.com/tu-usuario/enfermeria-backend.git
   cd enfermeria-backend
   ```

2. **Instala las dependencias:**

   ```bash
   composer install
   ```

3. **Configura el entorno:**

   - Copia el archivo `.env.example` a `.env.local`
   - Ajusta las variables de entorno (base de datos, usuario, contraseña, etc.)

4. **Crea la base de datos y ejecuta las migraciones:**

   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

5. **Inicia el servidor local:**

   ```bash
   symfony server:start
   ```

6. **(Opcional) Configura integración continua con GitHub Actions:**

   - Añade el workflow en `.github/workflows/ci.yml`
   - Al hacer un commit o pull request, se ejecutarán automáticamente las **pruebas unitarias**.

---

## 🚀 Uso / Ejemplos de API

### 🔹 Crear enfermero

**Request**
```http
POST /api/nurses
Content-Type: application/json

{
  "name": "Laura Martínez",
  "username": "lmartinez",
  "password": "clave123"
}
```

**Response**
- `201 Created`
- `400 Bad Request`

---

### 🔹 Obtener enfermero por ID

**Request**
```http
GET /api/nurses/{id}
```

**Response**
- `200 OK`
- `404 Not Found`

---

### 🔹 Actualizar enfermero

**Request**
```http
PUT /api/nurses/{id}
Content-Type: application/json

{
  "name": "Laura M. Martínez",
  "password": "nuevaClave456"
}
```

**Response**
- `200 OK`
- `400 Bad Request`
- `404 Not Found`

---

### 🔹 Eliminar enfermero

**Request**
```http
DELETE /api/nurses/{id}
```

**Response**
- `200 OK`
- `404 Not Found`

---

### 🔹 Login de enfermero

**Request**
```http
POST /api/nurses/login
Content-Type: application/json

{
  "username": "lmartinez",
  "password": "clave123"
}
```

**Response**
- `200 OK` → Devuelve token de sesión  
- `401 Unauthorized`

---

## 🧪 Pruebas

El proyecto incluye **pruebas unitarias automáticas** para verificar la correcta funcionalidad del CRUD y el login.  
Estas pruebas se ejecutan automáticamente mediante **GitHub Actions** o de forma manual con:

```bash
php bin/phpunit
```

---

## 📈 Próximas funcionalidades

- Gestión completa de **pacientes**
- Relación entre **enfermeros ↔ pacientes**
- Sistema de **roles y permisos**
- Mejora del **sistema de autenticación con JWT**
- Documentación completa de la API (Swagger / OpenAPI)

---

## 👨‍💻 Autor

**Nombre:** Tu nombre o equipo  
**Repositorio:** [https://github.com/tu-usuario/enfermeria-backend](https://github.com/tu-usuario/enfermeria-backend)  

---

## 🧰 Tecnologías utilizadas

- PHP 8.x  
- Symfony 6.x  
- Doctrine ORM  
- PHPUnit  
- GitHub Actions  
- MySQL / SQLite (según entorno)

---

💪 Proyecto desarrollado como base para la gestión de enfermería con buenas prácticas de desarrollo, testing e integración continua.
w
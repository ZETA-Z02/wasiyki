import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import api from "../api/axios";
import "./Inquilinos.css";

const Inquilinos = () => {
  const navigate = useNavigate();

  // Estados principales
  const [inquilinos, setInquilinos] = useState([]);
  const [habitacionesLibres, setHabitacionesLibres] = useState([]);
  const [loading, setLoading] = useState(true);
  const [busqueda, setBusqueda] = useState("");

  // Estados del Modal
  const [isModalOpen, setIsModalOpen] = useState(false);

  // Formularios (Separados lógicamente para la petición en cadena)
  const [formInquilino, setFormInquilino] = useState({
    nombre: "",
    apellido: "",
    dni: "",
    email: "",
    telefono: "",
    fecha_nacimiento: "",
  });

  const [formContrato, setFormContrato] = useState({
    habitacion_id: "",
    canon_mensual: "",
    tipo_contrato: "fijo",
    fecha_inicio: "",
    fecha_fin: "",
  });

  // Cargar datos al montar
  useEffect(() => {
    fetchDatos();
  }, []);

  const fetchDatos = async () => {
    setLoading(true);
    try {
      // Hacemos ambas peticiones en paralelo
      const [resInquilinos, resHabitaciones] = await Promise.all([
        api.get("/inquilinos"),
        api.get("/habitaciones/disponibles"),
      ]);
      setInquilinos(resInquilinos.data.data);
      setHabitacionesLibres(resHabitaciones.data.data);
    } catch (error) {
      console.error("Error cargando datos:", error);
    } finally {
      setLoading(false);
    }
  };

  // Manejo de inputs
  const handleInquilinoChange = (e) =>
    setFormInquilino({ ...formInquilino, [e.target.name]: e.target.value });
  const handleContratoChange = (e) =>
    setFormContrato({ ...formContrato, [e.target.name]: e.target.value });

  // Guardar: Crear Inquilino -> Crear Contrato
  const handleSubmit = async (e) => {
    e.preventDefault();

    try {
      // 1. Crear el Inquilino
      const resInquilino = await api.post("/inquilinos", formInquilino);
      const nuevoInquilinoId = resInquilino.data.data.id;

      // 2. Crear el Contrato con el ID del nuevo inquilino
      const payloadContrato = {
        ...formContrato,
        inquilino_id: nuevoInquilinoId,
        canon_mensual: parseFloat(formContrato.canon_mensual),
      };

      // Si es indefinido, limpiamos la fecha fin para evitar errores de validación
      if (payloadContrato.tipo_contrato === "indefinido") {
        delete payloadContrato.fecha_fin;
      }

      await api.post("/contratos", payloadContrato);

      // 3. Limpiar y recargar
      setIsModalOpen(false);
      setFormInquilino({
        nombre: "",
        apellido: "",
        dni: "",
        email: "",
        telefono: "",
        fecha_nacimiento: "",
      });
      setFormContrato({
        habitacion_id: "",
        canon_mensual: "",
        tipo_contrato: "fijo",
        fecha_inicio: "",
        fecha_fin: "",
      });
      fetchDatos();
    } catch (error) {
      console.error("Error en el registro:", error);
      alert(
        "Error al registrar. Verifica los datos y que la habitación esté disponible.",
      );
    }
  };

  // Filtro de búsqueda
  const inquilinosFiltrados = inquilinos.filter(
    (inq) =>
      inq.nombre.toLowerCase().includes(busqueda.toLowerCase()) ||
      inq.apellido.toLowerCase().includes(busqueda.toLowerCase()) ||
      inq.dni.includes(busqueda),
  );

  // Helper para el Avatar (Primeras letras)
  const getIniciales = (nombre, apellido) => {
    return `${nombre.charAt(0)}${apellido.charAt(0)}`.toUpperCase();
  };

  return (
    <div className="inquilinos-container">
      <div className="page-header">
        <h1 className="page-title">Gestión Detallada de Inquilinos</h1>
        <p className="page-subtitle">
          Administra los arrendatarios, sus contratos y verifica sus pagos.
        </p>

        <div className="toolbar">
          <div className="search-box">
            <span className="search-icon">🔍</span>
            <input
              type="text"
              placeholder="Buscar por nombre, apellido o DNI..."
              value={busqueda}
              onChange={(e) => setBusqueda(e.target.value)}
            />
          </div>
          <button
            className="btn-primary"
            onClick={() => setIsModalOpen(true)}
            style={{ margin: 0 }}
          >
            + Nuevo Inquilino
          </button>
        </div>
      </div>

      {/* Tabla Principal */}
      <div className="table-card">
        {loading ? (
          <p style={{ padding: "20px", textAlign: "center" }}>
            Cargando lista...
          </p>
        ) : (
          <table className="custom-table">
            <thead>
              <tr>
                <th>Nombre Completo</th>
                <th>DNI</th>
                <th>Correo Electrónico</th>
                <th>Teléfono</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              {inquilinosFiltrados.length > 0 ? (
                inquilinosFiltrados.map((inq) => (
                  <tr key={inq.id}>
                    <td>
                      <div className="tenant-cell">
                        <div className="avatar">
                          {getIniciales(inq.nombre, inq.apellido)}
                        </div>
                        <strong>
                          {inq.nombre} {inq.apellido}
                        </strong>
                      </div>
                    </td>
                    <td>{inq.dni}</td>
                    <td>{inq.email || "-"}</td>
                    <td>{inq.telefono || "-"}</td>
                    <td>
                      <div className="action-buttons">
                        {/* Botones de navegación usando React Router */}
                        <button
                          className="btn-table-action"
                          onClick={() =>
                            navigate(`/inquilinos/${inq.id}/contrato`)
                          }
                        >
                          📄 Contrato
                        </button>
                        <button
                          className="btn-table-action"
                          onClick={() =>
                            navigate(`/inquilinos/${inq.id}/pagos`)
                          }
                        >
                          💵 Pagos
                        </button>
                      </div>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td
                    colSpan="5"
                    style={{ textAlign: "center", padding: "30px" }}
                  >
                    No se encontraron inquilinos.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        )}
      </div>

      {/* Modal de Registro Integral (Inquilino + Contrato) */}
      {isModalOpen && (
        <div className="modal-overlay">
          <div className="modal-content" style={{ maxWidth: "800px" }}>
            <div className="modal-header">
              <h2>Registrar Nuevo Inquilino y Contrato</h2>
              <button
                className="close-btn"
                onClick={() => setIsModalOpen(false)}
              >
                ×
              </button>
            </div>

            <form onSubmit={handleSubmit}>
              <div className="modal-grid">
                {/* Columna 1: Datos Personales */}
                <div>
                  <h3 className="modal-section-title">1. Datos Personales</h3>

                  <div style={{ display: "flex", gap: "10px" }}>
                    <div className="form-group" style={{ flex: 1 }}>
                      <label>Nombre</label>
                      <input
                        type="text"
                        name="nombre"
                        value={formInquilino.nombre}
                        onChange={handleInquilinoChange}
                        required
                      />
                    </div>
                    <div className="form-group" style={{ flex: 1 }}>
                      <label>Apellido</label>
                      <input
                        type="text"
                        name="apellido"
                        value={formInquilino.apellido}
                        onChange={handleInquilinoChange}
                        required
                      />
                    </div>
                  </div>

                  <div className="form-group">
                    <label>DNI / Documento</label>
                    <input
                      type="text"
                      name="dni"
                      value={formInquilino.dni}
                      onChange={handleInquilinoChange}
                      required
                    />
                  </div>

                  <div className="form-group">
                    <label>Correo Electrónico</label>
                    <input
                      type="email"
                      name="email"
                      value={formInquilino.email}
                      onChange={handleInquilinoChange}
                    />
                  </div>

                  <div style={{ display: "flex", gap: "10px" }}>
                    <div className="form-group" style={{ flex: 1 }}>
                      <label>Teléfono</label>
                      <input
                        type="text"
                        name="telefono"
                        value={formInquilino.telefono}
                        onChange={handleInquilinoChange}
                      />
                    </div>
                    <div className="form-group" style={{ flex: 1 }}>
                      <label>Nacimiento (Opcional)</label>
                      <input
                        type="date"
                        name="fecha_nacimiento"
                        value={formInquilino.fecha_nacimiento}
                        onChange={handleInquilinoChange}
                      />
                    </div>
                  </div>
                </div>

                {/* Columna 2: Datos del Contrato */}
                <div>
                  <h3 className="modal-section-title">
                    2. Asignación de Habitación
                  </h3>

                  <div className="form-group">
                    <label>Habitación Disponible</label>
                    <select
                      name="habitacion_id"
                      value={formContrato.habitacion_id}
                      onChange={handleContratoChange}
                      required
                    >
                      <option value="">-- Seleccionar Habitación --</option>
                      {habitacionesLibres.map((hab) => (
                        <option key={hab.id} value={hab.id}>
                          Hab. {hab.numero} - S/ {hab.precio}
                        </option>
                      ))}
                    </select>
                  </div>

                  <div className="form-group">
                    <label>Canon Mensual Acordado (S/)</label>
                    <input
                      type="number"
                      step="0.01"
                      name="canon_mensual"
                      value={formContrato.canon_mensual}
                      onChange={handleContratoChange}
                      required
                    />
                  </div>

                  <div className="form-group">
                    <label>Tipo de Contrato</label>
                    <select
                      name="tipo_contrato"
                      value={formContrato.tipo_contrato}
                      onChange={handleContratoChange}
                      required
                    >
                      <option value="fijo">Plazo Fijo</option>
                      <option value="indefinido">Indefinido</option>
                    </select>
                  </div>

                  <div style={{ display: "flex", gap: "10px" }}>
                    <div className="form-group" style={{ flex: 1 }}>
                      <label>Fecha de Inicio</label>
                      <input
                        type="date"
                        name="fecha_inicio"
                        value={formContrato.fecha_inicio}
                        onChange={handleContratoChange}
                        required
                      />
                    </div>
                    {/* Solo mostrar Fecha Fin si es contrato fijo */}
                    {formContrato.tipo_contrato === "fijo" && (
                      <div className="form-group" style={{ flex: 1 }}>
                        <label>Fecha Fin</label>
                        <input
                          type="date"
                          name="fecha_fin"
                          value={formContrato.fecha_fin}
                          onChange={handleContratoChange}
                          required
                        />
                      </div>
                    )}
                  </div>
                </div>
              </div>

              <div
                className="form-actions"
                style={{
                  marginTop: "30px",
                  borderTop: "1px solid #eaeaea",
                  paddingTop: "20px",
                }}
              >
                <button
                  type="button"
                  className="btn-table-action"
                  onClick={() => setIsModalOpen(false)}
                  style={{ padding: "10px 20px", fontSize: "14px" }}
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="btn-primary"
                  style={{ margin: 0 }}
                >
                  Registrar Inquilino y Contrato
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};

export default Inquilinos;

import { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import api from "../api/axios";
import "./ContratoDetalle.css";

const ContratoDetalle = () => {
  const { id } = useParams(); // ID del Inquilino
  const navigate = useNavigate();

  const [loading, setLoading] = useState(true);
  const [inquilinoId, setInquilinoId] = useState(id);
  const [contratoActivo, setContratoActivo] = useState(null);

  // Estados de los formularios
  const [formInquilino, setFormInquilino] = useState({
    nombre: "",
    apellido: "",
    dni: "",
    email: "",
    telefono: "",
    fecha_nacimiento: "",
  });

  const [formContrato, setFormContrato] = useState({
    id: "",
    canon_mensual: "",
    estado_contrato: "",
    tipo_contrato: "",
    fecha_inicio: "",
    fecha_fin: "",
  });

  useEffect(() => {
    fetchDatos();
  }, [id]);

  const fetchDatos = async () => {
    setLoading(true);
    try {
      // 1. Obtener Inquilino
      const resInq = await api.get(`/inquilinos/${id}`);
      const dataInq = resInq.data.data;
      setFormInquilino({
        nombre: dataInq.nombre,
        apellido: dataInq.apellido,
        dni: dataInq.dni,
        email: dataInq.email || "",
        telefono: dataInq.telefono || "",
        fecha_nacimiento: dataInq.fecha_nacimiento || "",
      });

      // 2. Obtener Contratos y buscar el activo de este inquilino
      const resContratos = await api.get("/contratos");
      const contrato = resContratos.data.data.find(
        (c) =>
          c.inquilino_id === parseInt(id) && c.estado_contrato !== "finalizado",
      );

      if (contrato) {
        setContratoActivo(contrato);
        setFormContrato({
          id: contrato.id,
          canon_mensual: contrato.canon_mensual,
          estado_contrato: contrato.estado_contrato,
          tipo_contrato: contrato.tipo_contrato,
          fecha_inicio: contrato.fecha_inicio,
          fecha_fin: contrato.fecha_fin || "",
        });
      } else {
        setContratoActivo(null);
      }
    } catch (error) {
      console.error("Error cargando detalles:", error);
      alert("No se pudieron cargar los datos.");
      navigate("/inquilinos");
    } finally {
      setLoading(false);
    }
  };

  // --- Handlers de Inputs ---
  const handleInqChange = (e) =>
    setFormInquilino({ ...formInquilino, [e.target.name]: e.target.value });
  const handleContChange = (e) =>
    setFormContrato({ ...formContrato, [e.target.name]: e.target.value });

  // --- Actualizar Inquilino ---
  const handleUpdateInquilino = async (e) => {
    e.preventDefault();
    try {
      await api.put(`/inquilinos/${id}`, formInquilino);
      alert("Datos del inquilino actualizados correctamente.");
    } catch (error) {
      console.error(error);
      alert("Error al actualizar el inquilino.");
    }
  };

  // --- Actualizar Contrato ---
  const handleUpdateContrato = async (e) => {
    e.preventDefault();
    if (!contratoActivo) return;
    try {
      const payload = { ...formContrato };
      if (payload.tipo_contrato === "indefinido") payload.fecha_fin = null;

      await api.put(`/contratos/${contratoActivo.id}`, payload);
      alert("Contrato actualizado correctamente.");
      fetchDatos(); // Recargar para sincronizar estados si se finalizó manual
    } catch (error) {
      console.error(error);
      alert("Error al actualizar el contrato.");
    }
  };

  // --- Acciones Críticas Superiores ---
  const handleTerminarContrato = async () => {
    if (!contratoActivo) return;
    if (
      window.confirm(
        "¿Estás seguro de terminar este contrato inmediatamente? Esto liberará la habitación.",
      )
    ) {
      try {
        await api.post(`/contratos/${contratoActivo.id}/terminar`);
        alert("Contrato finalizado y habitación liberada.");
        fetchDatos(); // Recargamos para reflejar que ya no hay contrato activo
      } catch (error) {
        console.error(error);
        alert("Error al terminar el contrato.");
      }
    }
  };

  const handleEliminarInquilino = async () => {
    if (
      window.confirm("¿Dar de baja (eliminar) a este inquilino del sistema?")
    ) {
      try {
        await api.delete(`/inquilinos/${id}`);
        alert("Inquilino eliminado correctamente.");
        navigate("/inquilinos");
      } catch (error) {
        console.error(error);
        alert("Error al eliminar el inquilino.");
      }
    }
  };

  if (loading)
    return <p style={{ padding: "20px" }}>Cargando datos del inquilino...</p>;

  return (
    <div className="detalle-container">
      {/* Cabecera y Botones Críticos */}
      <div className="detalle-header">
        <div>
          <h1 className="detalle-title">
            Gestión y Detalle: {formInquilino.nombre} {formInquilino.apellido}
          </h1>
          <p className="detalle-subtitle">
            Ver datos personales, detalles del contrato y realizar
            actualizaciones.
          </p>
        </div>

        <div className="header-actions">
          <button className="btn-danger" onClick={handleEliminarInquilino}>
            🗑️ Dar de Baja Inquilino
          </button>
          {contratoActivo && (
            <button className="btn-warning" onClick={handleTerminarContrato}>
              🛑 Terminar Contrato
            </button>
          )}
        </div>
      </div>

      <div className="cards-grid">
        {/* ---------------------------------------------------
                    TARJETA 1: DATOS PERSONALES DEL INQUILINO 
                --------------------------------------------------- */}
        <form className="form-card" onSubmit={handleUpdateInquilino}>
          <div className="card-header">
            <div className="card-icon">🧑🏽</div>
            <h2 className="card-title">Datos Personales del Inquilino</h2>
          </div>

          <div className="form-grid">
            <div className="form-group-full">
              <label>Nombres</label>
              <input
                type="text"
                name="nombre"
                value={formInquilino.nombre}
                onChange={handleInqChange}
                required
              />
            </div>
            <div className="form-group-full">
              <label>Apellidos</label>
              <input
                type="text"
                name="apellido"
                value={formInquilino.apellido}
                onChange={handleInqChange}
                required
              />
            </div>
            <div className="form-group-full">
              <label>DNI / CE / Pasaporte</label>
              <input
                type="text"
                name="dni"
                value={formInquilino.dni}
                onChange={handleInqChange}
                required
              />
            </div>
            <div>
              <label>Teléfono</label>
              <input
                type="text"
                name="telefono"
                value={formInquilino.telefono}
                onChange={handleInqChange}
              />
            </div>
            <div>
              <label>Correo Electrónico</label>
              <input
                type="email"
                name="email"
                value={formInquilino.email}
                onChange={handleInqChange}
              />
            </div>
            <div className="form-group-full">
              <label>Fecha de Nacimiento</label>
              <input
                type="date"
                name="fecha_nacimiento"
                value={formInquilino.fecha_nacimiento}
                onChange={handleInqChange}
              />
            </div>
          </div>

          <button type="submit" className="btn-update">
            Guardar Cambios Inquilino
          </button>
        </form>

        {/* ---------------------------------------------------
                    TARJETA 2: DETALLES DEL CONTRATO 
                --------------------------------------------------- */}
        {contratoActivo ? (
          <form className="form-card" onSubmit={handleUpdateContrato}>
            <div className="card-header">
              <div className="card-icon">🔑</div>
              <h2 className="card-title">Detalles del Contrato Activo</h2>
            </div>

            <div className="form-grid">
              <div className="form-group-full">
                <label>Habitación Asignada (Solo lectura)</label>
                <input
                  type="text"
                  value={`Habitación ${contratoActivo.habitacion?.numero || "N/A"}`}
                  disabled
                />
              </div>

              <div>
                <label>Fecha de Inicio</label>
                <input
                  type="date"
                  name="fecha_inicio"
                  value={formContrato.fecha_inicio}
                  onChange={handleContChange}
                  required
                />
              </div>

              <div>
                <label>Fecha de Fin</label>
                <input
                  type="date"
                  name="fecha_fin"
                  value={formContrato.fecha_fin}
                  onChange={handleContChange}
                  disabled={formContrato.tipo_contrato === "indefinido"}
                  required={formContrato.tipo_contrato === "fijo"}
                />
              </div>

              <div>
                <label>Monto Mensual (S/.)</label>
                <input
                  type="number"
                  step="0.01"
                  name="canon_mensual"
                  value={formContrato.canon_mensual}
                  onChange={handleContChange}
                  required
                />
              </div>

              <div>
                <label>Tipo de Contrato</label>
                <select
                  name="tipo_contrato"
                  value={formContrato.tipo_contrato}
                  onChange={handleContChange}
                  required
                >
                  <option value="fijo">Plazo Fijo</option>
                  <option value="indefinido">Indefinido</option>
                </select>
              </div>

              <div className="form-group-full">
                <label>Estado del Contrato</label>
                <select
                  name="estado_contrato"
                  value={formContrato.estado_contrato}
                  onChange={handleContChange}
                  required
                >
                  <option value="activo">Vigente (Al Día)</option>
                  <option value="con_deuda">Con Deuda (Atrasado)</option>
                  <option value="finalizado">Finalizado</option>
                </select>
              </div>
            </div>

            <button type="submit" className="btn-update">
              Actualizar o Renovar Contrato
            </button>
          </form>
        ) : (
          <div
            className="form-card"
            style={{
              justifyContent: "center",
              alignItems: "center",
              textAlign: "center",
            }}
          >
            <div className="card-icon" style={{ marginBottom: "20px" }}>
              📄
            </div>
            <h2 className="card-title" style={{ marginBottom: "10px" }}>
              Sin Contrato Activo
            </h2>
            <p style={{ color: "var(--color-slate)", fontSize: "14px" }}>
              Este inquilino no posee un contrato vigente en este momento.
            </p>
            {/* Aquí podrías agregar un botón para "Crear Contrato" si lo deseas */}
          </div>
        )}
      </div>

      <button
        className="btn-back-bottom"
        onClick={() => navigate("/inquilinos")}
      >
        Volver al Listado de Inquilinos
      </button>
    </div>
  );
};

export default ContratoDetalle;

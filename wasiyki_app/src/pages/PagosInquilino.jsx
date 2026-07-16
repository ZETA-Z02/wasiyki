import { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import api from "../api/axios";
import "./PagosInquilino.css";

const PagosInquilino = () => {
  const { id } = useParams(); // ID del inquilino desde la URL
  const navigate = useNavigate();

  const [loading, setLoading] = useState(true);
  const [inquilino, setInquilino] = useState(null);
  const [contrato, setContrato] = useState(null);
  const [pagos, setPagos] = useState([]);
  const [isModalOpen, setIsModalOpen] = useState(false);

  // Estado del formulario de nuevo pago
  const [formData, setFormData] = useState({
    monto: "",
    fecha_pago: "",
    periodo: "",
    metodo_pago: "transferencia",
    numero_comprobante: "",
    observaciones: "",
  });

  useEffect(() => {
    fetchDatos();
  }, [id]);

  const fetchDatos = async () => {
    setLoading(true);
    try {
      // 1. Obtener datos del inquilino
      const resInq = await api.get(`/inquilinos/${id}`);
      setInquilino(resInq.data.data);

      // 2. Obtener contratos (En un caso real tendrías un endpoint específico,
      // aquí filtramos de la lista general para encontrar el suyo)
      const resContratos = await api.get("/contratos");
      const contratoActivo = resContratos.data.data.find(
        (c) =>
          c.inquilino_id === parseInt(id) && c.estado_contrato !== "finalizado",
      );

      setContrato(contratoActivo);

      // 3. Obtener pagos filtrados por ese contrato
      if (contratoActivo) {
        const resPagos = await api.get("/pagos");
        const susPagos = resPagos.data.data.filter(
          (p) => p.contrato_id === contratoActivo.id,
        );
        // Ordenar por fecha más reciente
        susPagos.sort(
          (a, b) => new Date(b.fecha_pago) - new Date(a.fecha_pago),
        );
        setPagos(susPagos);

        // Pre-llenar el monto del modal con el canon mensual
        setFormData((prev) => ({
          ...prev,
          monto: contratoActivo.canon_mensual,
        }));
      }
    } catch (error) {
      console.error("Error cargando historial:", error);
    } finally {
      setLoading(false);
    }
  };

  const handleInputChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const payload = { ...formData, contrato_id: contrato.id };
      await api.post("/pagos", payload);
      setIsModalOpen(false);

      // Limpiar formulario y recargar
      setFormData({
        monto: contrato.canon_mensual,
        fecha_pago: "",
        periodo: "",
        metodo_pago: "transferencia",
        numero_comprobante: "",
        observaciones: "",
      });
      fetchDatos();
    } catch (error) {
      console.error("Error registrando pago:", error);
      alert("No se pudo registrar el pago. Verifica los datos.");
    }
  };

  // Función vital para descargar PDFs desde el Backend API
  const descargarComprobante = async (pagoId, numeroComprobante) => {
    try {
      const response = await api.get(`/pagos/${pagoId}/comprobante`, {
        responseType: "blob", // Importante para manejar archivos
      });

      // Crear una URL temporal para el blob y forzar descarga
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement("a");
      link.href = url;
      link.setAttribute(
        "download",
        `comprobante_${numeroComprobante || pagoId}.pdf`,
      );
      document.body.appendChild(link);
      link.click();
      link.remove();
    } catch (error) {
      console.error("Error descargando comprobante:", error);
      alert("El comprobante no está disponible en este momento.");
    }
  };

  if (loading)
    return <p style={{ padding: "20px" }}>Cargando historial de pagos...</p>;
  if (!inquilino)
    return <p style={{ padding: "20px" }}>Inquilino no encontrado.</p>;

  return (
    <div className="pagos-container">
      {/* Botón de regreso */}
      <button
        onClick={() => navigate("/inquilinos")}
        style={{
          background: "none",
          border: "none",
          color: "var(--color-slate)",
          cursor: "pointer",
          marginBottom: "10px",
          fontSize: "14px",
        }}
      >
        ← Volver a Inquilinos
      </button>

      <div className="pagos-header">
        <div>
          <h1 className="pagos-title">
            Historial de Pagos: {inquilino.nombre} {inquilino.apellido}
          </h1>
          <p className="pagos-subtitle">
            Registrar y ver pagos de un inquilino específico
          </p>
        </div>
        {contrato && (
          <button
            className="btn-primary"
            onClick={() => setIsModalOpen(true)}
            style={{ margin: 0 }}
          >
            + Registrar Nuevo Pago
          </button>
        )}
      </div>

      {/* Tarjeta de Resumen */}
      {!contrato ? (
        <div
          className="summary-info-card"
          style={{ display: "block", borderLeftColor: "#c53030" }}
        >
          <p style={{ margin: 0, color: "#c53030", fontWeight: "bold" }}>
            Este inquilino no tiene un contrato activo asociado.
          </p>
        </div>
      ) : (
        <div className="summary-info-card">
          <div className="info-item">
            <span className="info-label">Inquilino:</span>
            {inquilino.nombre} {inquilino.apellido}
          </div>
          <div className="info-item">
            <span className="info-label">Monto Mensual:</span>
            S/{" "}
            {parseFloat(contrato.canon_mensual).toLocaleString("es-PE", {
              minimumFractionDigits: 2,
            })}
          </div>
          <div className="info-item">
            <span className="info-label">Habitación:</span>
            Hab. {contrato.habitacion?.numero || "Asignada"}
          </div>
          <div className="info-item">
            <span className="info-label">Estado Actual:</span>
            {contrato.estado_contrato === "con_deuda" ? (
              <span className="badge-status badge-atrasado">Atrasado</span>
            ) : (
              <span className="badge-status badge-aldia">Al Día</span>
            )}
          </div>
        </div>
      )}

      {/* Tabla de Pagos */}
      <h3 style={{ marginTop: "30px", color: "var(--color-dark-navy)" }}>
        Historial de Pagos Realizados
      </h3>
      <div className="pagos-table-wrapper">
        <table className="custom-table">
          <thead>
            <tr>
              <th>Fecha de Pago</th>
              <th>Concepto</th>
              <th>Monto (S/.)</th>
              <th>Método</th>
              <th>Comprobante</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            {pagos.length > 0 ? (
              pagos.map((pago) => (
                <tr key={pago.id}>
                  <td>{pago.fecha_pago}</td>
                  <td>{pago.periodo}</td>
                  <td>
                    S/{" "}
                    {parseFloat(pago.monto).toLocaleString("es-PE", {
                      minimumFractionDigits: 2,
                    })}
                  </td>
                  <td style={{ textTransform: "capitalize" }}>
                    {pago.metodo_pago}
                  </td>
                  <td>
                    <button
                      className="btn-comprobante"
                      onClick={() =>
                        descargarComprobante(pago.id, pago.numero_comprobante)
                      }
                    >
                      📄 Ver Comprobante
                    </button>
                  </td>
                  <td>
                    <button className="btn-table-action">👁️ Ver</button>
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td
                  colSpan="6"
                  style={{
                    textAlign: "center",
                    padding: "30px",
                    color: "var(--color-slate)",
                  }}
                >
                  No hay pagos registrados para este contrato.
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      {/* Modal para Registrar Nuevo Pago */}
      {isModalOpen && (
        <div className="modal-overlay">
          <div className="modal-content">
            <div className="modal-header">
              <h2>Registrar Pago</h2>
              <button
                className="close-btn"
                onClick={() => setIsModalOpen(false)}
              >
                ×
              </button>
            </div>

            <form onSubmit={handleSubmit}>
              <div style={{ display: "flex", gap: "15px" }}>
                <div className="form-group" style={{ flex: 1 }}>
                  <label>Monto (S/)</label>
                  <input
                    type="number"
                    step="0.01"
                    name="monto"
                    value={formData.monto}
                    onChange={handleInputChange}
                    required
                  />
                </div>
                <div className="form-group" style={{ flex: 1 }}>
                  <label>Fecha del Pago</label>
                  <input
                    type="date"
                    name="fecha_pago"
                    value={formData.fecha_pago}
                    onChange={handleInputChange}
                    required
                  />
                </div>
              </div>

              <div className="form-group">
                <label>Concepto / Período (Ej. Agosto 2026)</label>
                <input
                  type="text"
                  name="periodo"
                  value={formData.periodo}
                  onChange={handleInputChange}
                  required
                />
              </div>

              <div style={{ display: "flex", gap: "15px" }}>
                <div className="form-group" style={{ flex: 1 }}>
                  <label>Método de Pago</label>
                  <select
                    name="metodo_pago"
                    value={formData.metodo_pago}
                    onChange={handleInputChange}
                    required
                  >
                    <option value="efectivo">Efectivo</option>
                    <option value="transferencia">Transferencia BCP</option>
                    <option value="yape">Yape</option>
                    <option value="plin">Plin</option>
                    <option value="otro">Otro</option>
                  </select>
                </div>
                <div className="form-group" style={{ flex: 1 }}>
                  <label>N° Comprobante (Opcional)</label>
                  <input
                    type="text"
                    name="numero_comprobante"
                    value={formData.numero_comprobante}
                    onChange={handleInputChange}
                    placeholder="Auto-generado si está vacío"
                  />
                </div>
              </div>

              <div className="form-group">
                <label>Observaciones</label>
                <input
                  type="text"
                  name="observaciones"
                  value={formData.observaciones}
                  onChange={handleInputChange}
                  placeholder="Detalles adicionales..."
                />
              </div>

              <div className="form-actions">
                <button
                  type="button"
                  className="btn-table-action"
                  onClick={() => setIsModalOpen(false)}
                  style={{ padding: "10px 20px" }}
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="btn-primary"
                  style={{ margin: 0 }}
                >
                  Guardar Pago
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};

export default PagosInquilino;

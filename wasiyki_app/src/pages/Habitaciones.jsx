import { useState, useEffect } from "react";
import api from "../api/axios";
import "./Habitaciones.css";

const Habitaciones = () => {
  const [habitaciones, setHabitaciones] = useState([]);
  const [loading, setLoading] = useState(true);

  // Estados para filtros
  const [busqueda, setBusqueda] = useState("");
  const [filtroEstado, setFiltroEstado] = useState("todas"); // todas, disponible, ocupada, mantenimiento

  // Estados para el Modal
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [habitacionEditando, setHabitacionEditando] = useState(null);
  const [formData, setFormData] = useState({
    piso: "",
    numero: "",
    precio: "",
    descripcion: "",
    estado: "disponible",
  });

  // Imagen de Unsplash estática para todas las habitaciones
  const placeholderImg =
    "https://images.unsplash.com/photo-1522771731478-44bf104a8b4d?auto=format&fit=crop&w=400&q=80";

  // Cargar habitaciones
  const fetchHabitaciones = async () => {
    try {
      const response = await api.get("/habitaciones");
      setHabitaciones(response.data.data);
    } catch (error) {
      console.error("Error cargando habitaciones:", error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchHabitaciones();
  }, []);

  // Manejar inputs del formulario
  const handleInputChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  // Abrir modal para Crear
  const openCreateModal = () => {
    setHabitacionEditando(null);
    setFormData({
      piso: "",
      numero: "",
      precio: "",
      descripcion: "",
      estado: "disponible",
    });
    setIsModalOpen(true);
  };

  // Abrir modal para Editar
  const openEditModal = (hab) => {
    setHabitacionEditando(hab.id);
    setFormData({
      piso: hab.piso,
      numero: hab.numero,
      precio: hab.precio,
      descripcion: hab.descripcion || "",
      estado: hab.estado,
    });
    setIsModalOpen(true);
  };

  // Guardar (Crear o Editar)
  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (habitacionEditando) {
        await api.put(`/habitaciones/${habitacionEditando}`, formData);
      } else {
        await api.post("/habitaciones", formData);
      }
      setIsModalOpen(false);
      fetchHabitaciones(); // Recargar datos
    } catch (error) {
      console.error("Error guardando habitación:", error);
      alert("Ocurrió un error al guardar los datos.");
    }
  };

  // Eliminar
  const handleDelete = async (id) => {
    if (window.confirm("¿Estás seguro de eliminar esta habitación?")) {
      try {
        await api.delete(`/habitaciones/${id}`);
        fetchHabitaciones();
      } catch (error) {
        console.error("Error eliminando:", error);
      }
    }
  };

  // Cálculos para las tarjetas de resumen
  const total = habitaciones.length;
  const ocupadas = habitaciones.filter((h) => h.estado === "ocupada").length;
  const disponibles = habitaciones.filter(
    (h) => h.estado === "disponible",
  ).length;
  const mantenimiento = habitaciones.filter(
    (h) => h.estado === "mantenimiento",
  ).length;

  // Filtrar la lista a renderizar
  const habitacionesFiltradas = habitaciones.filter((h) => {
    const coincideBusqueda = h.numero
      .toLowerCase()
      .includes(busqueda.toLowerCase());
    const coincideEstado =
      filtroEstado === "todas" || h.estado === filtroEstado;
    return coincideBusqueda && coincideEstado;
  });

  if (loading)
    return <p style={{ padding: "20px" }}>Cargando habitaciones...</p>;

  return (
    <div className="habitaciones-container">
      <div className="header-actions">
        <h1 style={{ margin: 0, color: "var(--color-dark-navy)" }}>
          Gestión de Habitaciones
        </h1>
        <button
          className="btn-primary"
          onClick={openCreateModal}
          style={{ marginTop: 0 }}
        >
          + Nueva Habitación
        </button>
      </div>

      {/* Tarjetas de Resumen */}
      <div className="summary-grid">
        <div className="summary-card">
          <h3>Total Habitaciones</h3>
          <p className="big-number">{total}</p>
        </div>
        <div className="summary-card">
          <h3>Ocupadas</h3>
          <p className="big-number" style={{ color: "var(--color-mustard)" }}>
            {ocupadas}
          </p>
        </div>
        <div className="summary-card">
          <h3>Disponibles</h3>
          <p className="big-number" style={{ color: "#48bb78" }}>
            {disponibles}
          </p>
        </div>
        <div className="summary-card">
          <h3>En Mantenimiento</h3>
          <p className="big-number" style={{ color: "#4299e1" }}>
            {mantenimiento}
          </p>
        </div>
      </div>

      {/* Barra de Filtros */}
      <div className="filters-section">
        <input
          type="text"
          placeholder="🔍 Buscar por número..."
          className="search-input"
          value={busqueda}
          onChange={(e) => setBusqueda(e.target.value)}
        />
        <div className="status-filters">
          <button
            className={`filter-btn ${filtroEstado === "todas" ? "active" : ""}`}
            onClick={() => setFiltroEstado("todas")}
          >
            Todas
          </button>
          <button
            className={`filter-btn ${filtroEstado === "disponible" ? "active" : ""}`}
            onClick={() => setFiltroEstado("disponible")}
          >
            Libres
          </button>
          <button
            className={`filter-btn ${filtroEstado === "ocupada" ? "active" : ""}`}
            onClick={() => setFiltroEstado("ocupada")}
          >
            Ocupadas
          </button>
          <button
            className={`filter-btn ${filtroEstado === "mantenimiento" ? "active" : ""}`}
            onClick={() => setFiltroEstado("mantenimiento")}
          >
            Mantenimiento
          </button>
        </div>
      </div>

      {/* Grid de Habitaciones */}
      <div className="rooms-grid">
        {habitacionesFiltradas.map((hab) => (
          <div key={hab.id} className="room-card">
            <img src={placeholderImg} alt="Habitación" className="room-image" />

            {/* Etiqueta dinámica de estado */}
            <div className={`status-badge status-${hab.estado}`}>
              {hab.estado === "disponible" ? "LIBRE" : hab.estado}
            </div>

            <div className="room-info">
              <h3 className="room-title">Hab. {hab.numero}</h3>
              <p className="room-price">
                S/{" "}
                {parseFloat(hab.precio).toLocaleString("es-PE", {
                  minimumFractionDigits: 2,
                })}{" "}
                / mes
              </p>
              <p className="room-desc">
                {hab.descripcion || "Sin descripción adicional."}
              </p>

              <div className="room-actions">
                <button
                  className="btn-small btn-edit"
                  onClick={() => openEditModal(hab)}
                >
                  Editar
                </button>
                <button
                  className="btn-small btn-delete"
                  onClick={() => handleDelete(hab.id)}
                >
                  Eliminar
                </button>
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Modal Crear/Editar */}
      {isModalOpen && (
        <div className="modal-overlay">
          <div className="modal-content">
            <div className="modal-header">
              <h2>
                {habitacionEditando ? "Editar Habitación" : "Nueva Habitación"}
              </h2>
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
                  <label>N° Habitación</label>
                  <input
                    type="text"
                    name="numero"
                    value={formData.numero}
                    onChange={handleInputChange}
                    required
                  />
                </div>
                <div className="form-group" style={{ flex: 1 }}>
                  <label>Piso</label>
                  <input
                    type="number"
                    name="piso"
                    value={formData.piso}
                    onChange={handleInputChange}
                    required
                  />
                </div>
              </div>

              <div className="form-group">
                <label>Precio Mensual (S/)</label>
                <input
                  type="number"
                  step="0.01"
                  name="precio"
                  value={formData.precio}
                  onChange={handleInputChange}
                  required
                />
              </div>

              <div className="form-group">
                <label>Estado</label>
                <select
                  name="estado"
                  value={formData.estado}
                  onChange={handleInputChange}
                  required
                >
                  <option value="disponible">Disponible</option>
                  <option value="ocupada">Ocupada</option>
                  <option value="mantenimiento">Mantenimiento</option>
                </select>
              </div>

              <div className="form-group">
                <label>Descripción</label>
                <input
                  type="text"
                  name="descripcion"
                  value={formData.descripcion}
                  onChange={handleInputChange}
                  placeholder="Ej. Con baño propio y ventana"
                />
              </div>

              <div className="form-actions">
                <button
                  type="button"
                  className="btn-small btn-edit"
                  onClick={() => setIsModalOpen(false)}
                  style={{ padding: "10px 20px", flex: "none" }}
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  className="btn-primary"
                  style={{ marginTop: 0 }}
                >
                  Guardar
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};

export default Habitaciones;

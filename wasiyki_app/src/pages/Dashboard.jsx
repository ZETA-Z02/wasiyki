import { useState, useEffect, useContext } from "react";
import { AuthContext } from "../context/AuthContext";
import api from "../api/axios";
import "./Dashboard.css";

const Dashboard = () => {
  const { user } = useContext(AuthContext);

  // Estados para manejar los datos de la API
  const [datos, setDatos] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // Efecto para solicitar los datos al montar el componente
  useEffect(() => {
    const fetchDashboardData = async () => {
      try {
        // Hacemos la petición al endpoint protegido
        const response = await api.get("/dashboard");
        setDatos(response.data);
        setError(null);
      } catch (err) {
        console.error("Error cargando el dashboard:", err);
        setError("No se pudo cargar la información del panel.");
      } finally {
        setLoading(false);
      }
    };

    fetchDashboardData();
  }, []);

  // Pantalla de carga mientras se resuelve la petición
  if (loading) {
    return (
      <div
        className="dashboard-container"
        style={{
          display: "flex",
          justifyContent: "center",
          alignItems: "center",
          height: "60vh",
        }}
      >
        <p style={{ color: "#3c5b74", fontSize: "18px", fontWeight: "600" }}>
          Cargando tu resumen...
        </p>
      </div>
    );
  }

  // Pantalla de error si falla la conexión
  if (error) {
    return (
      <div className="dashboard-container">
        <div
          style={{
            backgroundColor: "#fee2e2",
            color: "#b91c1c",
            padding: "15px",
            borderRadius: "10px",
          }}
        >
          {error}
        </div>
      </div>
    );
  }

  // Cálculo seguro por si las habitaciones totales vienen en 0 (evitar división por cero)
  const porcentajeOcupacion =
    datos.habitacionesTotales > 0
      ? (datos.habitacionesOcupadas / datos.habitacionesTotales) * 100
      : 0;

  return (
    <div className="dashboard-container">
      {/* Cabecera */}
      <div className="welcome-header">
        <div>
          <h1 className="welcome-title">
            ¡Hola, {user?.nombre || "Arrendador"}!
          </h1>
          <p className="welcome-subtitle">
            Aquí está el resumen de tu propiedad hoy.
          </p>
        </div>
      </div>

      {/* Grid de Tarjetas */}
      <div className="dashboard-grid">
        {/* Card: Ingresos */}
        <div className="dash-card">
          <h2 className="card-title">Ingresos Mensuales</h2>
          {/* Usamos S/ como moneda basándonos en la respuesta de tu API */}
          <div className="income-amount">
            S/{" "}
            {datos.ingresosMes.toLocaleString("es-PE", {
              minimumFractionDigits: 2,
            })}
          </div>
          <span style={{ fontSize: "12px", color: "#3c5b74" }}>
            Datos en tiempo real
          </span>
        </div>

        {/* Card: Resumen de Propiedad */}
        <div className="dash-card">
          <h2 className="card-title">Resumen de Propiedad</h2>
          <p className="progress-text">
            Tienes{" "}
            <strong>
              {datos.habitacionesOcupadas}/{datos.habitacionesTotales}
            </strong>{" "}
            habitaciones ocupadas.
          </p>
          <div className="progress-bar-bg">
            <div
              className="progress-bar-fill"
              style={{ width: `${porcentajeOcupacion}%` }}
            ></div>
          </div>
        </div>

        {/* Card: Alertas y Vencimientos */}
        <div className="dash-card">
          <h2 className="card-title">Alertas de Pago</h2>
          {datos.alertas.length > 0 ? (
            <ul className="alert-list">
              {datos.alertas.map((alerta) => (
                <li key={alerta.id} className="alert-item">
                  <span>{alerta.mensaje}</span>
                  {alerta.tipo === "atraso" ? (
                    <span className="alert-danger">
                      Atraso ({alerta.monto})
                    </span>
                  ) : (
                    <span className="alert-warning">Vence: {alerta.fecha}</span>
                  )}
                </li>
              ))}
            </ul>
          ) : (
            <p style={{ color: "#a3acb1", fontSize: "14px", margin: 0 }}>
              Todo al día, no hay alertas.
            </p>
          )}
        </div>

        {/* Card: Habitaciones Disponibles */}
        <div className="dash-card">
          <h2 className="card-title">Disponibles ahora</h2>
          <div className="room-chips">
            {datos.disponibles.length > 0 ? (
              datos.disponibles.map((hab, index) => (
                <span key={index} className="room-chip">
                  {hab}
                </span>
              ))
            ) : (
              <span style={{ color: "#a3acb1", fontSize: "14px" }}>
                Inmueble 100% ocupado
              </span>
            )}
          </div>
        </div>

        {/* Card: Acciones Rápidas */}
        <div className="dash-card">
          <h2 className="card-title">Acciones Rápidas</h2>
          <div className="quick-actions">
            <button className="btn-action">
              <span>+</span> Registrar Pago
            </button>
            <button className="btn-action">
              <span>+</span> Nuevo Inquilino
            </button>
          </div>
        </div>
      </div>

      {/* Sección Inferior: Últimos Pagos */}
      <div className="dash-card">
        <h2 className="card-title">Últimos Pagos Realizados</h2>
        <div className="table-container">
          {datos.ultimosPagos.length > 0 ? (
            <table className="payments-table">
              <thead>
                <tr>
                  <th>Inquilino</th>
                  <th>Habitación</th>
                  <th>Fecha</th>
                  <th>Método</th>
                  <th>Monto</th>
                  <th>Estado</th>
                </tr>
              </thead>
              <tbody>
                {datos.ultimosPagos.map((pago) => (
                  <tr key={pago.id}>
                    <td>
                      <strong>{pago.inquilino}</strong>
                    </td>
                    <td>{pago.habitacion}</td>
                    <td>{pago.fecha}</td>
                    <td>{pago.metodo}</td>
                    <td>
                      S/{" "}
                      {pago.monto.toLocaleString("es-PE", {
                        minimumFractionDigits: 2,
                      })}
                    </td>
                    <td>
                      <span className="badge badge-success">Completado</span>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          ) : (
            <p
              style={{
                color: "#a3acb1",
                fontSize: "14px",
                textAlign: "center",
                padding: "20px 0",
              }}
            >
              No hay pagos registrados recientemente.
            </p>
          )}
        </div>
      </div>
    </div>
  );
};

export default Dashboard;

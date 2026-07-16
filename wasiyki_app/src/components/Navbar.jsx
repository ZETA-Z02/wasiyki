import { NavLink } from "react-router-dom";
import { useContext } from "react";
import { AuthContext } from "../context/AuthContext";
import "./Navbar.css";

const Navbar = () => {
  // Extraemos la función de cierre de sesión del contexto global
  const { logout } = useContext(AuthContext);

  return (
    <nav className="navbar">
      {/* Logo (Solo visible en PC) */}
      <div className="navbar-brand">Wasiyki</div>

      {/* Enlaces de Navegación */}
      <div className="navbar-links">
        <NavLink
          to="/dashboard"
          className={({ isActive }) =>
            isActive ? "nav-link active" : "nav-link"
          }
        >
          Inicio
        </NavLink>

        <NavLink
          to="/inquilinos"
          className={({ isActive }) =>
            isActive ? "nav-link active" : "nav-link"
          }
        >
          Inquilinos
        </NavLink>

        <NavLink
          to="/habitaciones"
          className={({ isActive }) =>
            isActive ? "nav-link active" : "nav-link"
          }
        >
          Habitaciones
        </NavLink>
      </div>

      {/* Acciones (Solo visible en PC) */}
      <div className="navbar-actions">
        <button className="action-btn" title="Notificaciones">
          🔔
        </button>

        {/* Botón de Logout */}
        <button
          className="action-btn"
          onClick={logout}
          title="Cerrar Sesión"
          style={{ display: "flex", alignItems: "center", gap: "8px" }}
        >
          👤
          <span style={{ fontSize: "14px", fontWeight: "500" }}>Salir</span>
        </button>
      </div>
    </nav>
  );
};

export default Navbar;

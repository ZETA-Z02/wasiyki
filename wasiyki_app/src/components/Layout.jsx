import Navbar from "./Navbar";
import { useContext } from "react";
import { AuthContext } from "../context/AuthContext";

const Layout = ({ children }) => {
  const { logout } = useContext(AuthContext);

  return (
    // El padding bottom evita que el contenido quede tapado por el menú en móviles.
    // En un archivo CSS global podrías ajustar este padding según la pantalla.
    <div
      style={{ minHeight: "100vh", paddingBottom: "70px", paddingTop: "10px" }}
    >
      {/* Cabecera superior simple (opcional, para el botón de salir) */}
      <header
        style={{
          display: "flex",
          justifyContent: "flex-end",
          padding: "0 20px",
        }}
      >
        <button
          onClick={logout}
          style={{
            background: "transparent",
            border: "none",
            color: "red",
            cursor: "pointer",
          }}
        >
          Cerrar Sesión
        </button>
      </header>

      <Navbar />

      {/* Aquí se renderizará el contenido de cada página (Dashboard, Inquilinos, etc.) */}
      <main style={{ padding: "20px" }}>{children}</main>
    </div>
  );
};

export default Layout;

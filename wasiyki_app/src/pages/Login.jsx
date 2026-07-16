import { useState, useContext } from "react";
import { AuthContext } from "../context/AuthContext";
import { useNavigate } from "react-router-dom";
import { useGoogleLogin } from "@react-oauth/google";
import api from "../api/axios";
import "./Login.css"; // Asegúrate de importar el CSS

const Login = () => {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");

  const { login } = useContext(AuthContext);
  const navigate = useNavigate();

  // 1. Inicio de sesión tradicional
  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");
    try {
      await login(email, password);
      navigate("/dashboard");
    } catch (err) {
      setError("Credenciales incorrectas o error de conexión.");
    }
  };

  // 2. Inicio de sesión con Google (PKCE)
  const iniciarSesionGoogle = useGoogleLogin({
    flow: "auth-code",
    onSuccess: async (codeResponse) => {
      try {
        const response = await api.post("/auth/google/pkce", {
          code: codeResponse.code,
          redirect_uri: "postmessage",
        });
        localStorage.setItem("token", response.data.token);
        // Si tienes una función en el AuthContext para actualizar el usuario manualmente,
        // llámala aquí, o simplemente recarga/redirige para que el useEffect lo detecte.
        window.location.href = "/dashboard";
      } catch (err) {
        setError("Fallo al sincronizar con el servidor de Wasiyki.");
        console.error(err);
      }
    },
    onError: () => {
      setError("El inicio de sesión con Google fue cancelado.");
    },
  });

  return (
    <div className="login-container">
      <div className="login-card">
        <h1 className="login-title">Wasiyki</h1>
        <p className="login-subtitle">Gestión inteligente de tus alquileres</p>

        {error && <div className="error-message">{error}</div>}

        <form className="login-form" onSubmit={handleSubmit}>
          <div className="input-group">
            <label>Correo Electrónico</label>
            <input
              type="email"
              placeholder="tucorreo@ejemplo.com"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
            />
          </div>

          <div className="input-group">
            <label>Contraseña</label>
            <input
              type="password"
              placeholder="••••••••"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
            />
          </div>

          <button type="submit" className="btn-primary">
            Ingresar
          </button>
        </form>

        <div className="divider">O continuar con</div>

        <button
          onClick={() => iniciarSesionGoogle()}
          className="btn-google"
          type="button"
        >
          <img
            src="https://img.icons8.com/color/20/000000/google-logo.png"
            alt="Google"
          />
          Google
        </button>

        <div className="login-card-footer">
          Al continuar, aceptas nuestros{" "}
          <a href="/terminos" target="_blank" rel="noopener noreferrer">
            Términos de Servicio
          </a>{" "}
          y nuestra{" "}
          <a href="/politica" target="_blank" rel="noopener noreferrer">
            Política de Privacidad
          </a>.
        </div>
      </div>
    </div>
  );
};

export default Login;

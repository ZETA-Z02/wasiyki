import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { useContext } from 'react';
import { AuthContext, AuthProvider } from './context/AuthContext';
import { GoogleOAuthProvider } from '@react-oauth/google';

// Componentes y Páginas
import Login from './pages/Login';
import Layout from './components/Layout';

import Dashboard from "./pages/Dashboard";
import Habitaciones from "./pages/Habitaciones";
import Inquilinos from "./pages/Inquilinos";
import PagosInquilino from "./pages/PagosInquilino";
import ContratoDetalle from "./pages/ContratoDetalle";


const GOOGLE_CLIENT_ID =
  "856406844150-el447lf3q51oi28k4l1usvbbu33n8fbn.apps.googleusercontent.com";

// Guardián de rutas protegidas
const ProtectedRoute = ({ children }) => {
    const { user, loading } = useContext(AuthContext);
    
    if (loading) return <p style={{ padding: '20px' }}>Cargando sesión...</p>;
    if (!user) return <Navigate to="/login" replace />;
    
    // Si está autenticado, lo envolvemos en el Layout que tiene el Navbar
    return <Layout>{children}</Layout>;
};

const AppRoutes = () => {
    return (
      <Router>
        <Routes>
          <Route path="/login" element={<Login />} />

          {/* Rutas protegidas que usan el Layout con el Navbar */}
          <Route
            path="/dashboard"
            element={
              <ProtectedRoute>
                <Dashboard />
              </ProtectedRoute>
            }
          />
          <Route
            path="/inquilinos"
            element={
              <ProtectedRoute>
                <Inquilinos />
              </ProtectedRoute>
            }
          />
          <Route
            path="/habitaciones"
            element={
              <ProtectedRoute>
                <Habitaciones />
              </ProtectedRoute>
            }
          />
          <Route
            path="/inquilinos/:id/contrato"
            element={
              <ProtectedRoute>
                <ContratoDetalle />
              </ProtectedRoute>
            }
          />
          <Route
            path="/inquilinos/:id/pagos"
            element={
              <ProtectedRoute>
                <PagosInquilino />
              </ProtectedRoute>
            }
          />

          {/* Redirección por defecto */}
          <Route path="*" element={<Navigate to="/login" replace />} />
        </Routes>
      </Router>
    );
};

function App() {
    return (
        <GoogleOAuthProvider clientId={GOOGLE_CLIENT_ID}>
            <AuthProvider>
                <AppRoutes />
            </AuthProvider>
        </GoogleOAuthProvider>
    );
}

export default App;

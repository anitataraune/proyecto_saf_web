import React from 'react';
import ReactDOM from 'react-dom/client';

import { BrowserRouter } from 'react-router-dom';

import App from './App';

import './estilos/global.css';

import { ProveedorAutenticacion } from './contexto/ContextoAutenticacion';
import { ProveedorConfiguracion } from './contexto/ContextoConfiguracion';

ReactDOM.createRoot(
    document.getElementById('root')
).render(

    <BrowserRouter>
        <ProveedorConfiguracion>
            <ProveedorAutenticacion>
                <App />
            </ProveedorAutenticacion>
        </ProveedorConfiguracion>
    </BrowserRouter>
);

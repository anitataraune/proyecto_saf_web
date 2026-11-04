import { useContext } from 'react';

import Enrutador from './rutas/Enrutador';

import {
    ContextoAutenticacion
} from './contexto/ContextoAutenticacion';

function App() {

    const { autenticado } =
        useContext(
            ContextoAutenticacion
        );

    return (
        <Enrutador
            autenticado={autenticado}
        />
    );
}

export default App;

export const reglasTipoCambio = {
    fecha: {
        required: 'La fecha es obligatoria.',
    },
    ufvs: {
        required: 'El valor de la UFV es obligatorio.',
        valueAsNumber: true,
        min: { value: 0, message: 'El valor no puede ser negativo.' },
    },
    valor_tc: {
        required: 'El tipo de cambio es obligatorio.',
        valueAsNumber: true,
        min: { value: 0, message: 'El tipo de cambio no puede ser negativo.' },
    },
};
export default function CampoTexto({
    label,
    name,
    type = 'text',
    register,
    errors,
    validation = {},
    ...props
}) {
    return (
        <div className="mb-4">
            {label && (
                <label className="block text-sm font-medium text-slate-700 mb-1">{label}</label>
            )}
            <input
                type={type}
                className={`w-full rounded-lg border bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-4 transition ${
                    errors?.[name]
                        ? 'border-rose-400 bg-rose-50/50 focus:border-rose-500 focus:ring-rose-100'
                        : 'border-slate-300 hover:border-slate-400 focus:border-blue-600 focus:ring-blue-100'
                }`}
                {...register(name, validation)}
                {...props}
            />
            {errors?.[name] && (
                <p className="text-rose-600 text-xs mt-1 font-medium">{errors[name].message}</p>
            )}
        </div>
    );
}

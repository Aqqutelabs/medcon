import React from 'react'

export default function FormInput({ label, type = 'text', name, value, onChange, error, placeholder }: any) {
  return (
    <div className="mb-3">
      <label className="block text-sm font-medium mb-1">{label}</label>
      <input
        className={`w-full border rounded px-3 py-2 ${error ? 'border-red-500' : 'border-gray-300'}`}
        type={type}
        name={name}
        value={value}
        onChange={onChange}
        placeholder={placeholder}
      />
      {error && <div className="text-sm text-red-600 mt-1">{error}</div>}
    </div>
  )
}

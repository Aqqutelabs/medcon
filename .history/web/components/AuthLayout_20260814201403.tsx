import React from 'react'

export default function AuthLayout({ children, title }: { children: React.ReactNode; title?: string }) {
  return (
    <div className="min-h-screen flex items-center justify-center p-4">
      <div className="auth-container">
        <h1 className="text-2xl font-bold text-heading mb-4">{title || 'Medcon Edu'}</h1>
        {children}
      </div>
    </div>
  )
}

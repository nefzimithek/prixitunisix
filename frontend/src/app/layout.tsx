import type { Metadata } from 'next'
import './globals.css'

export const metadata: Metadata = {
  title: 'PrixTunisix — Comparateur de prix en Tunisie',
  description:
    'Comparez les prix de milliers de produits sur les meilleures boutiques tunisiennes en ligne.',
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="fr">
      <body className="min-h-screen bg-gray-50 text-gray-900 antialiased">
        {children}
      </body>
    </html>
  )
}

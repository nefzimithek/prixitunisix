import Link from 'next/link'

export default function HomePage() {
  return (
    <main className="flex min-h-screen flex-col items-center justify-center bg-gradient-to-br from-brand-700 to-brand-900 px-4 text-white">
      <h1 className="mb-4 text-5xl font-extrabold tracking-tight">
        Prix<span className="text-yellow-300">Tunisix</span>
      </h1>
      <p className="mb-8 max-w-xl text-center text-lg text-blue-100">
        Comparez les prix de milliers de produits sur les meilleures boutiques
        tunisiennes en ligne — MyTek, Tunisianet, SFax Computer et plus encore.
      </p>

      <div className="flex gap-4">
        <Link
          href="/products"
          className="rounded-xl bg-white px-6 py-3 font-semibold text-brand-700 shadow hover:bg-blue-50 transition"
        >
          Parcourir les produits
        </Link>
        <Link
          href="/login"
          className="rounded-xl border border-white px-6 py-3 font-semibold text-white hover:bg-white/10 transition"
        >
          Connexion
        </Link>
      </div>
    </main>
  )
}

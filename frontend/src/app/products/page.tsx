import Link from 'next/link'
import api from '@/lib/api'

interface Product {
  id: number
  name: string
  slug: string
  image_url: string | null
  category: { name: string }
  brand: { name: string } | null
}

async function getProducts(q?: string) {
  const params = q ? `?q=${encodeURIComponent(q)}` : ''
  const { data } = await api.get(`/products${params}`)
  return data
}

export default async function ProductsPage({
  searchParams,
}: {
  searchParams: { q?: string }
}) {
  const { data: products } = await getProducts(searchParams.q)

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <header className="bg-brand-700 px-6 py-4 text-white shadow">
        <div className="mx-auto flex max-w-7xl items-center justify-between">
          <Link href="/" className="text-xl font-extrabold">
            Prix<span className="text-yellow-300">Tunisix</span>
          </Link>
          <form method="get" className="flex gap-2">
            <input
              name="q"
              defaultValue={searchParams.q}
              placeholder="Rechercher un produit..."
              className="w-72 rounded-lg px-3 py-1.5 text-sm text-gray-800 focus:outline-none"
            />
            <button
              type="submit"
              className="rounded-lg bg-white px-4 py-1.5 text-sm font-medium text-brand-700 hover:bg-blue-50"
            >
              Chercher
            </button>
          </form>
          <Link href="/login" className="text-sm font-medium hover:underline">
            Connexion
          </Link>
        </div>
      </header>

      <main className="mx-auto max-w-7xl px-6 py-10">
        <h1 className="mb-6 text-2xl font-bold text-gray-800">
          {searchParams.q
            ? `Résultats pour "${searchParams.q}"`
            : 'Tous les produits'}
        </h1>

        {products.length === 0 ? (
          <p className="text-gray-500">Aucun produit trouvé.</p>
        ) : (
          <div className="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-4">
            {products.map((product: Product) => (
              <Link
                key={product.id}
                href={`/products/${product.id}`}
                className="group rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100 hover:shadow-md hover:ring-brand-200 transition"
              >
                <div className="mb-3 flex h-40 items-center justify-center rounded-xl bg-gray-100">
                  {product.image_url ? (
                    // eslint-disable-next-line @next/next/no-img-element
                    <img
                      src={product.image_url}
                      alt={product.name}
                      className="max-h-36 object-contain"
                    />
                  ) : (
                    <span className="text-4xl">📦</span>
                  )}
                </div>
                <p className="text-xs font-medium text-brand-600">
                  {product.brand?.name ?? ''} · {product.category.name}
                </p>
                <h3 className="mt-1 line-clamp-2 text-sm font-semibold text-gray-800 group-hover:text-brand-700">
                  {product.name}
                </h3>
              </Link>
            ))}
          </div>
        )}
      </main>
    </div>
  )
}

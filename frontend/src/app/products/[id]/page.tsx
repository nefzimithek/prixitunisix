import Link from 'next/link'
import api from '@/lib/api'
import PriceHistoryChart from '@/components/PriceHistoryChart'

interface Offer {
  id: number
  price: number
  is_available: boolean
  merchant_url: string
  merchant_website: { name: string; logo_url: string | null } | null
  discount: { discounted_price: number; value: number; type: string } | null
}

interface Product {
  id: number
  name: string
  description: string | null
  image_url: string | null
  specifications: Record<string, string> | null
  category: { name: string }
  brand: { name: string } | null
  offers: Offer[]
}

async function getProduct(id: string): Promise<Product> {
  const { data } = await api.get(`/products/${id}`)
  return data
}

export default async function ProductDetailPage({
  params,
}: {
  params: { id: string }
}) {
  const product = await getProduct(params.id)

  return (
    <div className="min-h-screen bg-gray-50">
      <header className="bg-brand-700 px-6 py-4 text-white shadow">
        <div className="mx-auto flex max-w-7xl items-center gap-4">
          <Link href="/" className="text-xl font-extrabold">
            Prix<span className="text-yellow-300">Tunisix</span>
          </Link>
          <Link href="/products" className="text-sm text-blue-200 hover:underline">
            ← Tous les produits
          </Link>
        </div>
      </header>

      <main className="mx-auto max-w-5xl px-6 py-10">
        <div className="grid gap-8 lg:grid-cols-3">
          {/* Product info */}
          <div className="lg:col-span-1">
            <div className="flex h-56 items-center justify-center rounded-2xl bg-white shadow-sm ring-1 ring-gray-100">
              {product.image_url ? (
                // eslint-disable-next-line @next/next/no-img-element
                <img
                  src={product.image_url}
                  alt={product.name}
                  className="max-h-52 object-contain"
                />
              ) : (
                <span className="text-6xl">📦</span>
              )}
            </div>

            {product.specifications && (
              <div className="mt-4 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
                <h3 className="mb-3 font-semibold text-gray-700">Caractéristiques</h3>
                <dl className="space-y-1 text-sm">
                  {Object.entries(product.specifications).map(([k, v]) => (
                    <div key={k} className="flex gap-2">
                      <dt className="w-24 shrink-0 font-medium capitalize text-gray-500">
                        {k}
                      </dt>
                      <dd className="text-gray-800">{v}</dd>
                    </div>
                  ))}
                </dl>
              </div>
            )}
          </div>

          {/* Offers + chart */}
          <div className="lg:col-span-2 space-y-6">
            <div>
              <p className="text-sm font-medium text-brand-600">
                {product.brand?.name} · {product.category.name}
              </p>
              <h1 className="mt-1 text-2xl font-bold text-gray-900">{product.name}</h1>
              {product.description && (
                <p className="mt-2 text-sm text-gray-600">{product.description}</p>
              )}
            </div>

            {/* Price history chart */}
            {product.offers[0] && (
              <div className="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
                <h3 className="mb-3 font-semibold text-gray-700">Historique des prix</h3>
                <PriceHistoryChart offerId={product.offers[0].id} />
              </div>
            )}

            {/* Offers comparison */}
            <div className="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
              <h3 className="mb-3 font-semibold text-gray-700">
                Comparer les prix ({product.offers.length} offres)
              </h3>
              <div className="space-y-3">
                {product.offers
                  .filter((o) => o.is_available)
                  .sort((a, b) => a.price - b.price)
                  .map((offer) => (
                    <div
                      key={offer.id}
                      className="flex items-center justify-between rounded-xl bg-gray-50 px-4 py-3"
                    >
                      <div>
                        <p className="text-sm font-medium text-gray-700">
                          {offer.merchant_website?.name ?? 'Marchand inconnu'}
                        </p>
                        {offer.discount && (
                          <p className="text-xs text-red-500 line-through">
                            {offer.price.toFixed(3)} TND
                          </p>
                        )}
                        <p className="text-lg font-bold text-brand-700">
                          {(offer.discount?.discounted_price ?? offer.price).toFixed(3)} TND
                        </p>
                      </div>
                      <a
                        href={`/api/offers/${offer.id}/redirect`}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700 transition"
                      >
                        Voir l&apos;offre →
                      </a>
                    </div>
                  ))}
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  )
}

'use client'

import { useEffect, useState } from 'react'
import {
  LineChart,
  Line,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
} from 'recharts'
import api from '@/lib/api'

interface PricePoint {
  price: number
  recorded_at: string
}

export default function PriceHistoryChart({ offerId }: { offerId: number }) {
  const [data, setData] = useState<PricePoint[]>([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    api
      .get(`/offers/${offerId}/price-history`)
      .then(({ data }) => setData(data))
      .catch(() => setData([]))
      .finally(() => setLoading(false))
  }, [offerId])

  if (loading) return <div className="h-40 animate-pulse rounded-lg bg-gray-100" />
  if (data.length === 0) return <p className="text-sm text-gray-400">Pas de données</p>

  const chartData = data.map((d) => ({
    date: new Date(d.recorded_at).toLocaleDateString('fr-TN', {
      day: '2-digit',
      month: 'short',
    }),
    prix: Number(d.price.toFixed(3)),
  }))

  return (
    <ResponsiveContainer width="100%" height={180}>
      <LineChart data={chartData}>
        <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
        <XAxis dataKey="date" tick={{ fontSize: 11 }} />
        <YAxis
          tick={{ fontSize: 11 }}
          tickFormatter={(v) => `${v} TND`}
          width={80}
        />
        <Tooltip formatter={(v: number) => [`${v.toFixed(3)} TND`, 'Prix']} />
        <Line
          type="monotone"
          dataKey="prix"
          stroke="#2563eb"
          strokeWidth={2}
          dot={false}
          activeDot={{ r: 4 }}
        />
      </LineChart>
    </ResponsiveContainer>
  )
}

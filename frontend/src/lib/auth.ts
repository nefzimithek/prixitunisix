import Cookies from 'js-cookie'
import api from './api'

export interface User {
  id: number
  name: string
  prename: string
  email: string
  role: 'client' | 'merchant' | 'employee' | 'admin'
}

export async function login(email: string, password: string): Promise<User> {
  const { data } = await api.post('/auth/login', { email, password })
  Cookies.set('auth_token', data.token, { expires: 7, sameSite: 'lax' })
  return data.user
}

export async function register(
  name: string,
  prename: string,
  email: string,
  password: string,
  password_confirmation: string
): Promise<User> {
  const { data } = await api.post('/auth/register', {
    name,
    prename,
    email,
    password,
    password_confirmation,
  })
  Cookies.set('auth_token', data.token, { expires: 7, sameSite: 'lax' })
  return data.user
}

export async function logout(): Promise<void> {
  await api.post('/auth/logout')
  Cookies.remove('auth_token')
}

export async function getMe(): Promise<User> {
  const { data } = await api.get('/auth/me')
  return data
}

export function isLoggedIn(): boolean {
  return !!Cookies.get('auth_token')
}

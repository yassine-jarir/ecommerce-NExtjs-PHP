// In your middleware.ts
import { NextRequest, NextResponse } from 'next/server';

export async function middleware(req) {
  const { pathname } = req.nextUrl;
  
  const authRoutes = ['/auth/sign-in', '/auth/sign-up'];
  const adminRoutes = ['/dashboard', '/dashboard/profile', '/dashboard/customers'];
  const clientRoutes = ['/client'];

  try {
    const userCookie = req.cookies.get('user');
    
    if (!userCookie && [...adminRoutes, ...clientRoutes].some(route => pathname.startsWith(route))) {
      return NextResponse.redirect(new URL('/auth/sign-in', req.url));
    }

    if (userCookie) {
      const userData = JSON.parse(userCookie.value);
      
      // Prevent authenticated users from accessing auth routes
      if (authRoutes.includes(pathname)) {
        return NextResponse.redirect(new URL(userData.role === 'admin' ?? '/dashboard' , req.url));
      }

      // Prevent clients from accessing admin routes
      if (userData.role === 'client' && adminRoutes.some(route => pathname.startsWith(route))) {
        return NextResponse.redirect(new URL('/client', req.url));
      }

      // Prevent admins from accessing client-only routes
      if (userData.role === 'admin' && clientRoutes.some(route => pathname === route)) {
        return NextResponse.redirect(new URL('/dashboard', req.url));
      }
    }

    return NextResponse.next();
  } catch (error) {
    // return NextResponse.redirect(new URL('/auth/sign-in', req.url));
  }
}

export const config = {
  matcher: [
    '/dashboard/:path*',
    '/auth/sign-in',
    '/auth/sign-up',
    // '/client/:path*',
  ],
};
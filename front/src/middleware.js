import { NextRequest, NextResponse } from 'next/server';

export async function middleware(req) {
  const { pathname } = req.nextUrl;
  
   const authRoutes = ['/auth/sign-in', '/auth/sign-up'];
  const protectedRoutes = ['/dashboard', '/dashboard/profile', '/dashboard/customers'];

  try {
    
     const userCookie = req.cookies.get('user');  

     if (!userCookie && protectedRoutes.some(route => pathname.startsWith(route))) {
      console.log('Unauthenticated access to protected route, redirecting to login...');
      return NextResponse.redirect(new URL('/auth/sign-in', req.url));
    }

     if (userCookie && authRoutes.includes(pathname)) {
      console.log('Authenticated user trying to access auth route, redirecting to dashboard...');
      return NextResponse.redirect(new URL('/dashboard', req.url));
    }

     if (!userCookie && protectedRoutes.some(route => pathname.startsWith(route))) {
      console.log('Unauthenticated user trying to access protected route, redirecting to login...');
      return NextResponse.redirect(new URL('/auth/sign-in', req.url));
    }

     return NextResponse.next();
  } catch (error) {
     return NextResponse.redirect(new URL('/auth/sign-in', req.url));
  }
}

 export const config = {
  matcher: [
    '/dashboard/:path*',
 
    '/auth/sign-in',
    '/auth/sign-up',
  ],
};

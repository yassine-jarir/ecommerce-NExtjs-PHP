import * as React from 'react';
import type { Viewport } from 'next';

import '@/styles/global.css';

 import { LocalizationProvider } from '@/components/core/localization-provider';
import { ThemeProvider } from '@/components/core/theme-provider/theme-provider';
import { AuthProvider } from '@/contexts/AuthContexts';

export const viewport = { width: 'device-width', initialScale: 1 } satisfies Viewport;

interface LayoutProps {
  children: React.ReactNode;
}

export default function Layout({ children }: LayoutProps): React.JSX.Element {
  return (
    <html lang="en">
      <body>
        <LocalizationProvider>
             <ThemeProvider>
              
            <AuthProvider>
              {children}
            </AuthProvider>
              
              
              </ThemeProvider>
         </LocalizationProvider>
      </body>
    </html>
  );
}

'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { useRouter } from 'next/navigation';

import { useAuth } from '@/contexts/AuthContexts';

function Navbar() {
  const [isCartOpen, setIsCartOpen] = useState(false);
  const [isAccountOpen, setIsAccountOpen] = useState(false);
  const router = useRouter();
  const { user, logout } = useAuth();

  const handleLogout = async () => {
    try {
      await logout();
      // router.push('/auth/sign-in');
    } catch (error) {
      console.error('Logout failed:', error);
    }
  };

  const toggleCart = () => {
    setIsCartOpen(!isCartOpen);
    if (isAccountOpen) setIsAccountOpen(false);
  };

  const toggleAccount = () => {
    setIsAccountOpen(!isAccountOpen);
    if (isCartOpen) setIsCartOpen(false);
  };

  return (
    <nav className="bg-gray-50 dark:bg-gray-800 antialiased relative">
      <div className="max-w-screen-xl px-4 mx-auto 2xl:px-0 py-4">
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-8">
            <ul className="hidden lg:flex items-center justify-start gap-6 md:gap-8 py-3 sm:justify-center">
              <Link href="/" className="text-xl font-bold text-gray-800">
                EcomCraft
              </Link>
            </ul>
          </div>

          <div className="flex items-center lg:space-x-2">
            {!user ? (
              <div className="flex items-center space-x-4">
                <Link href="/auth/sign-in" className="text-gray-600 hover:text-gray-900">
                  Sign In
                </Link>
                <Link href="/auth/sign-up" className="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                  Sign Up
                </Link>
              </div>
            ) : (
              <div className="relative">
                <button
                  onClick={toggleAccount}
                  type="button"
                  className="inline-flex items-center rounded-lg justify-center p-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm font-medium leading-none text-gray-900 dark:text-white"
                >
                  <svg
                    className="w-5 h-5 me-1"
                    aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke="currentColor"
                      strokeWidth="2"
                      d="M7 17v1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1a3 3 0 0 0-3-3h-4a3 3 0 0 0-3 3Zm8-9a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                    />
                  </svg>
                  Account
                  <svg
                    className="w-4 h-4 text-gray-900 dark:text-white ms-1"
                    aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke="currentColor"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      strokeWidth="2"
                      d="m19 9-7 7-7-7"
                    />
                  </svg>
                </button>

                {isAccountOpen && (
                  <div className="absolute top-full right-0 mt-2 z-10 w-56 divide-y divide-gray-100 overflow-hidden rounded-lg bg-white antialiased shadow dark:divide-gray-600 dark:bg-gray-700">
                    <ul className="p-2 text-start text-sm font-medium text-gray-900 dark:text-white">
                      <li>
                        <a
                          href="#"
                          className="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"
                        >
                          My Account
                        </a>
                      </li>
                      <li>
                        <a
                          href="#"
                          className="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"
                        >
                          My Orders
                        </a>
                      </li>
                    </ul>

                    <div className="p-2 text-sm font-medium text-gray-900 dark:text-white">
                      <button
                        onClick={handleLogout}
                        className="inline-flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"
                      >
                        Sign Out
                      </button>
                    </div>
                  </div>
                )}
              </div>
            )}
          </div>
        </div>
      </div>
    </nav>
  );
}

export default Navbar;

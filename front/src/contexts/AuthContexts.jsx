'use client';

import { createContext, useContext, useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import axios from 'axios';

const AuthContext = createContext(undefined);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const router = useRouter();

  const checkAuth = async () => {
    try {
      const response = await axios.get('http://localhost:4040/api.php?route=check-auth', {
        withCredentials: true,
      });
      if (response.data.authenticated) {
        setUser(response.data.user);
      } else {
        setUser(null);
      }
    } catch (error) {
      setUser(null);
    } finally {
      setLoading(false);
    }
  };

  const login = async (email, password) => {
    try {
      const response = await axios.post(
        'http://localhost:4040/api.php?route=login',
        { email, password },
        { withCredentials: true }
      );

      if (response.data.user) {
        setUser(response.data.user);
        return response.data;
      } else {
        throw new Error(response.data.error || 'Login failed');
      }
    } catch (error) {
      throw new Error(error.response?.data?.error || error.message || 'Login failed');
    }
  };

  const signup = async (name, email, password) => {
    try {
      const response = await axios.post(
        'http://localhost:4040/api.php?route=signup',
        { name, email, password },
        { withCredentials: true }
      );

      if (response.data.user) {
        return response.data;
      } else {
        throw new Error(response.data.error || 'Signup failed');
      }
    } catch (error) {
      throw new Error(error.response?.data?.error || error.message || 'Signup failed');
    }
  };

  const logout = async () => {
    try {
      await axios.post('http://localhost:4040/api.php?route=logout', {}, { withCredentials: true });

      setUser(null);
      router.push('/auth/sign-in');
      router.refresh(); // Force refresh to update authentication state
    } catch (error) {
      console.error('Logout error:', error);
      throw error;
    }
  };

  useEffect(() => {
    checkAuth();
  }, []);

  return (
    <AuthContext.Provider value={{ user, loading, login, logout, signup, checkAuth }}>{children}</AuthContext.Provider>
  );
}

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};

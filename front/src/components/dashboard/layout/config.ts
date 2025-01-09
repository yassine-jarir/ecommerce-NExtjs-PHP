import type { NavItemConfig } from '@/types/nav';
import { paths } from '@/paths';

export const navItems = [
  { key: 'overview', title: 'Vue d\'ensemble', href: paths.dashboard.overview, icon: 'chart-pie' },
  { key: 'Gestion des Clients', title: 'Gestion des Clients', href: paths.dashboard.customers, icon: 'users' },
  { key: 'Gestion des Produits', title: ' Gestion des Produits', href: paths.dashboard.Products, icon: 'plugs-connected' },
  { key: 'Gestion des Commandes', title: 'Gestion des Commandes', href: paths.dashboard.settings, icon: 'gear-six' },
  { key: 'Profile', title: 'Profile', href: paths.dashboard.account, icon: 'user' },
 ] satisfies NavItemConfig[];

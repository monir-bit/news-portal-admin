import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import {route} from 'ziggy-js';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import {
    LayoutDashboard,
    FolderTree,
    Newspaper,
    Flame,
    Megaphone,
    BookOpen,
    Folder,
    Radio,
    Tag,
} from 'lucide-react';

import AppLogo from './app-logo';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
        icon: LayoutDashboard,
    },
    {
        title: 'Category',
        href: route('category.index'),
        icon: FolderTree,
    },
    {
        title: 'Tag',
        href: route('tag.index'),
        icon: Tag,
    },
    {
        title: 'News',
        href: route('news.index'),
        icon: Newspaper,
    },
    {
        title: 'Latest News',
        href: route('latest-news.index'),
        icon: Flame,
    },
    {
        title: 'Marque News',
        href: route('marque-news.index'),
        icon: Megaphone,
    },
    {
        title: 'Live News',
        href: route('live-news.index'),
        icon: Radio,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/react-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#react',
        icon: BookOpen,
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={route('dashboard')} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}

import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout';
import { type BreadcrumbItem, SharedData } from '@/types';
import { type ReactNode, useEffect } from 'react';
import { toast } from 'sonner';
import { usePage } from '@inertiajs/react';

interface AppLayoutProps {
    children: ReactNode;
    breadcrumbs?: BreadcrumbItem[];
}

export default ({ children, breadcrumbs, ...props }: AppLayoutProps) => {

    const {flash} = usePage<SharedData>().props

    useEffect(() => {
        if (flash.success) {
            toast.success(flash.success);
        }
        if (flash.error) {
            toast.error(flash.error);
        }
    }, [flash])
    return (
        <AppLayoutTemplate breadcrumbs={breadcrumbs} {...props}>
            <div className="p-3">{children}</div>
        </AppLayoutTemplate>
    );
}

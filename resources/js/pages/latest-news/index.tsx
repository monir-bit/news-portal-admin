import React, { useState } from 'react';
import { DataTable } from '@/components/shared/data-table';
import { LatestNewsType } from '@/types/models/latest-news-type';
import { BreadcrumbItem, Column } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { route } from 'ziggy-js';
import { Button } from '@/components/ui/button';
import DeleteModal from '@/components/shared/delete-modal';

type PageProps = {
    latestNews: LatestNewsType[];
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
];

const Index = () => {
    const { latestNews } = usePage<PageProps>().props;
    const [selectedLatestNews, setSelectedLatestNews] = useState<LatestNewsType | null>(null);
    const [openDeleteModal, setOpenDeleteModal] = useState(false);

    const handleDelete = () => {
        if (!selectedLatestNews) return;
        router.delete(route('latest-news.delete', { id: selectedLatestNews.id }), {
            onFinish: () => {
                setOpenDeleteModal(false);
                setSelectedLatestNews(null);
            },
        });
    };

    const columns: Column<LatestNewsType>[] = [
        {
            key: 'position',
            header: 'Position',
            sortable: true,
            render: (val) => (
                <span className="rounded-full bg-slate-100 px-2 py-1 text-xs font-bold tracking-wider text-slate-600 uppercase">
                    {val}
                </span>
            ),
        },
        {
            key: 'news',
            header: 'News Title',
            sortable: true,
            render: (val) => (
                <span className="font-medium text-slate-900">
                    {val?.title || 'N/A'}
                </span>
            ),
        },
        {
            key: '',
            header: 'Action',
            sortable: false,
            render: (val, latestNews) => {
                const handleDeleteModal = () => {
                    setOpenDeleteModal(true);
                    setSelectedLatestNews(latestNews);
                };

                return (
                    <div className="flex gap-2">
                        <Button
                            onClick={handleDeleteModal}
                            variant="destructive"
                            size="sm"
                        >
                            Delete
                        </Button>
                    </div>
                );
            },
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Latest News" />

            <div className="mb-6 flex items-center justify-between">
                <h2 className="text-2xl font-bold text-slate-900">
                    Latest News
                </h2>
            </div>

            <DataTable
                columns={columns}
                data={latestNews}
                loading={false}
                pageSize={10}
            />

            <DeleteModal
                open={openDeleteModal}
                setOpen={setOpenDeleteModal}
                onConfirm={handleDelete}
            />
        </AppLayout>
    );
};

export default Index;

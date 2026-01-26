import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { NewsType } from '@/types/models/news-type';
import { NewsTimelineType } from '@/types/models/news-timeline-type';
import FormInput from '@/components/shared/form-input';
import TextEditor from '@/components/shared/text-editor';
import { Button } from '@/components/ui/button';
import { useState } from 'react';
import DeleteModal from '@/components/shared/delete-modal';
import { Pencil, Trash2 } from 'lucide-react';

type PageProps = {
    news: NewsType;
    timelineNews: NewsTimelineType[];
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
    {
        title: 'News',
        href: route('news.index'),
    },
];

const Index = () => {
    const { news, timelineNews } = usePage<PageProps>().props;
    const [editingTimeline, setEditingTimeline] = useState<NewsTimelineType | null>(null);
    const [selectedTimeline, setSelectedTimeline] = useState<NewsTimelineType | null>(null);
    const [openDeleteModal, setOpenDeleteModal] = useState(false);

    const { data, setData, post, processing, reset, errors } = useForm({
        news_id: news.id,
        title: '',
        details: '',
        date: new Date().toISOString().split('T')[0],
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        if (editingTimeline) {
            post(route('timeline-news.update', { news_id: news.id, id: editingTimeline.id }), {
                onSuccess: () => {
                    reset();
                    setEditingTimeline(null);
                },
            });
        } else {
            post(route('timeline-news.store', { news_id: news.id }), {
                onSuccess: () => {
                    reset();
                },
            });
        }
    };

    const handleEdit = (timeline: NewsTimelineType) => {
        setEditingTimeline(timeline);
        setData({
            news_id: timeline.news_id,
            title: timeline.title,
            details: timeline.details,
            date: new Date(timeline.date).toISOString().split('T')[0],
        });
    };

    const handleCancelEdit = () => {
        setEditingTimeline(null);
        reset();
    };

    const handleDelete = () => {
        if (!selectedTimeline) return;
        router.delete(
            route('timeline-news.delete', { news_id: news.id, id: selectedTimeline.id }),
            {
                onFinish: () => {
                    setOpenDeleteModal(false);
                    setSelectedTimeline(null);
                },
            },
        );
    };

    const handleDeleteModal = (timeline: NewsTimelineType) => {
        setSelectedTimeline(timeline);
        setOpenDeleteModal(true);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Timeline News" />

            <div className="mb-6">
                <h2 className="text-2xl font-bold text-slate-900">Timeline News</h2>
                <p className="text-sm text-slate-600 mt-1">Manage timeline entries for: <span className="font-semibold">{news.title}</span></p>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Left Side - Form */}
                <div className="bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
                    <h3 className="text-lg font-semibold text-slate-800 mb-4">
                        {editingTimeline ? 'Edit Timeline Entry' : 'Create New Timeline Entry'}
                    </h3>

                    <form onSubmit={handleSubmit} className="space-y-4">
                        <FormInput
                            label="Title"
                            required
                            value={data.title}
                            error={errors.title}
                            onChange={(e) => setData('title', e.target.value)}
                            placeholder="Enter timeline title"
                        />

                        <TextEditor
                            value={data.details}
                            onChange={(value) => setData('details', value)}
                            error={errors.details}
                            label="Details"
                            required
                            height={200}
                            placeholder="Enter timeline details..."
                        />

                        <FormInput
                            label="Date"
                            type="date"
                            required
                            value={data.date}
                            error={errors.date}
                            onChange={(e) => setData('date', e.target.value)}
                        />

                        <div className="flex gap-2 pt-2">
                            <Button
                                className="flex-1"
                                type="submit"
                                disabled={processing}
                            >
                                {processing ? 'Saving...' : editingTimeline ? 'Update Timeline' : 'Create Timeline'}
                            </Button>

                            {editingTimeline && (
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={handleCancelEdit}
                                >
                                    Cancel
                                </Button>
                            )}
                        </div>
                    </form>
                </div>

                {/* Right Side - Timeline List */}
                <div className="bg-white rounded-lg border border-slate-200 p-6 shadow-sm">
                    <h3 className="text-lg font-semibold text-slate-800 mb-4">
                        Timeline Entries ({timelineNews.length})
                    </h3>

                    {timelineNews.length === 0 ? (
                        <div className="text-center py-12 text-slate-400">
                            <p>No timeline entries yet.</p>
                            <p className="text-sm mt-2">Create your first entry using the form.</p>
                        </div>
                    ) : (
                        <div className="space-y-4 max-h-[600px] overflow-y-auto pr-2">
                            {timelineNews.map((timeline) => (
                                <div
                                    key={timeline.id}
                                    className={`p-4 border rounded-lg shadow-sm transition-all ${
                                        editingTimeline?.id === timeline.id
                                            ? 'border-blue-500 bg-blue-50'
                                            : 'border-slate-200 hover:border-slate-300'
                                    }`}
                                >
                                    <div className="flex justify-between items-start mb-2">
                                        <h4 className="text-md font-semibold text-slate-900 flex-1">
                                            {timeline.title}
                                        </h4>
                                        <div className="flex gap-1 ml-2">
                                            <Button
                                                size="sm"
                                                variant="ghost"
                                                onClick={() => handleEdit(timeline)}
                                                className="h-8 w-8 p-0"
                                            >
                                                <Pencil className="h-4 w-4" />
                                            </Button>
                                            <Button
                                                size="sm"
                                                variant="ghost"
                                                onClick={() => handleDeleteModal(timeline)}
                                                className="h-8 w-8 p-0 text-red-600 hover:text-red-700 hover:bg-red-50"
                                            >
                                                <Trash2 className="h-4 w-4" />
                                            </Button>
                                        </div>
                                    </div>

                                    <div
                                        className="text-sm text-slate-700 mb-2 prose prose-sm max-w-none"
                                        dangerouslySetInnerHTML={{ __html: timeline.details }}
                                    />

                                    <div className="flex items-center justify-between mt-3 pt-3 border-t border-slate-100">
                                        <span className="text-xs text-slate-500 font-medium">
                                            {new Date(timeline.date).toLocaleDateString('en-US', {
                                                year: 'numeric',
                                                month: 'long',
                                                day: 'numeric',
                                            })}
                                        </span>
                                        <span className="text-xs text-slate-400">
                                            ID: {timeline.id}
                                        </span>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </div>

            <DeleteModal
                open={openDeleteModal}
                setOpen={setOpenDeleteModal}
                onConfirm={handleDelete}
                title="Delete Timeline Entry?"
                description="This action cannot be undone. This will permanently delete the timeline entry."
            />
        </AppLayout>
    );
};


export default Index;

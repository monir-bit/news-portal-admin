import FormInput from '@/components/shared/form-input';
import FormSwitch from '@/components/shared/form-switch';
import FormFileImage from '@/components/shared/form-file-image';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';
import TextEditor from '@/components/shared/text-editor';
import TagInput from '@/components/shared/tag-input';
import { FileType } from '@/types';
import FormSelect from '@/components/shared/form-select';
import {
    AllSectionLayoutType,
    SectionLayoutOrderSequenceType,
} from '@/types/applications/all-section-layout-type';
import { useState } from 'react';
import { CategoryListType } from '@/types/applications/category-list-type';

interface Props {
    tags: string[];
    categories: CategoryListType[];
    sectionLayouts: AllSectionLayoutType[];
}

export default function CreateNews({ categories, tags, sectionLayouts }: Props) {
    const [newsPosition, setNewsPosition] = useState<SectionLayoutOrderSequenceType>([]);

    const { data, setData, post, processing, reset, errors } = useForm({
        shoulder: '',
        title: '',
        ticker: '',
        sort_description: '',
        proofreader: 0,
        image: null as FileType,
        timeline_id: null as number | null,
        published: false,
        latest: false,
        news_marquee: false,
        live_news: false,
        is_visible_shoulder: true,
        is_visible_ticker: true,
        date: new Date().toISOString().split('T')[0],
        details: '',
        tags: [] as string[],
        category_id: null as number | null,
        section_layout_id: '',
        section_layout_news_position: null as number | null,
        is_pinned: false,
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post(route('news.store'), {
            onFinish: () => {
                reset();
            },
        });
    }

    const handleChangeSectionLayout = (value: string) => {
        setData('section_layout_id', value);
        const selectedLayout = sectionLayouts.find((item) => item.id == value);
        if (selectedLayout) {
            setNewsPosition(selectedLayout.order_sequence);
        }
    };

    return (
        <AppLayout>
            <div className="mx-auto space-y-6 border p-4">
                <h1 className="text-xl font-semibold">Create News</h1>

                <form onSubmit={submit} className="space-y-4">
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <FormInput
                                label="Shoulder"
                                value={data.shoulder}
                                error={errors.shoulder}
                                onChange={(e) =>
                                    setData('shoulder', e.target.value)
                                }
                            />

                            <FormInput
                                label="Title"
                                required
                                value={data.title}
                                error={errors.title}
                                onChange={(e) =>
                                    setData('title', e.target.value)
                                }
                            />

                            <FormInput
                                label="Ticker"
                                value={data.ticker}
                                error={errors.ticker}
                                onChange={(e) =>
                                    setData('ticker', e.target.value)
                                }
                            />

                            <FormInput
                                label="Date"
                                type="date"
                                required
                                value={data.date}
                                error={errors.date}
                                onChange={(e) =>
                                    setData('date', e.target.value)
                                }
                            />

                            <TagInput
                                label="Tags"
                                suggestions={tags}
                                value={data.tags}
                                onChange={(value) => setData('tags', value)}
                                error={errors.tags}
                                helperText="Type to search or add custom tags"
                                placeholder="Type and press Enter"
                            />

                            <TextEditor
                                containerClassName="col-span-2"
                                value={data.sort_description}
                                onChange={(value) =>
                                    setData('sort_description', value)
                                }
                                label="Sort Description"
                                error={errors.sort_description}
                            />

                            <div className="mt-3 grid grid-cols-2 gap-4">
                                <FormSwitch
                                    label="Published"
                                    description="Publish this news"
                                    checked={data.published}
                                    error={errors.published}
                                    onChange={(checked) =>
                                        setData('published', checked)
                                    }
                                />

                                <FormSwitch
                                    label="Latest"
                                    description="Mark as latest news"
                                    checked={data.latest}
                                    error={errors.latest}
                                    onChange={(checked) =>
                                        setData('latest', checked)
                                    }
                                />

                                <FormSwitch
                                    label="News Marquee"
                                    description="Show in news marquee"
                                    checked={data.news_marquee}
                                    error={errors.news_marquee}
                                    onChange={(checked) =>
                                        setData('news_marquee', checked)
                                    }
                                />

                                <FormSwitch
                                    label="Live News"
                                    description="Mark as live news"
                                    checked={data.live_news}
                                    error={errors.live_news}
                                    onChange={(checked) =>
                                        setData('live_news', checked)
                                    }
                                />

                                <FormSwitch
                                    label="Visible Shoulder"
                                    description="Show shoulder field"
                                    checked={data.is_visible_shoulder}
                                    error={errors.is_visible_shoulder}
                                    onChange={(checked) =>
                                        setData('is_visible_shoulder', checked)
                                    }
                                />

                                <FormSwitch
                                    label="Visible Ticker"
                                    description="Show ticker field"
                                    checked={data.is_visible_ticker}
                                    error={errors.is_visible_ticker}
                                    onChange={(checked) =>
                                        setData('is_visible_ticker', checked)
                                    }
                                />
                            </div>
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <FormSelect
                                options={categories.map((item) => ({
                                    label: item.name,
                                    value: item.id,
                                }))}
                                onChange={(value) => setData('category_id', Number(value))}
                                label="Select Category"
                            />

                            <FormSelect
                                options={sectionLayouts.map((item) => ({
                                    label: item.name,
                                    value: item.id,
                                }))}
                                onChange={handleChangeSectionLayout}
                                label="Select Section"
                            />

                            <FormSelect
                                options={newsPosition.map((item) => ({
                                    label: item.toString(),
                                    value: item,
                                }))}
                                onChange={(value) =>
                                    setData(
                                        'section_layout_news_position',
                                        Number(value),
                                    )
                                }
                            />

                            <FormSwitch
                                label="Pined News"
                                description="Mark as replace news"
                                checked={data.is_pinned}
                                error={errors.is_pinned}
                                onChange={(checked) =>
                                    setData('is_pinned', checked)
                                }
                            />

                            <FormFileImage
                                label="Image"
                                imageUrl={data.image as string}
                                error={errors.image}
                                onChange={(file) => setData('image', file)}
                                helperText="Upload an image or drag and drop"
                            />

                            <TextEditor
                                value={data.details}
                                onChange={(value) => setData('details', value)}
                                error={errors.details}
                                containerClassName="col-span-2"
                                label="Description"
                            />
                        </div>
                    </div>
                    <Button
                        className="mt-4 w-full"
                        type="submit"
                        disabled={processing}
                    >
                        Save News
                    </Button>
                </form>
            </div>
        </AppLayout>
    );
}


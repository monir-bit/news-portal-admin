import { useEffect, useState } from 'react';
import { RecursiveCategory } from '@/types/applications/recursive-category-type';

type Props = {
    data: RecursiveCategory[];
    value: number[]; // ⬅ selected ids from parent
    onChange: (ids: number[]) => void;
};

export default function MultiSelectCategoryTree({
    data,
    value,
    onChange,
}: Props) {
    const [selected, setSelected] = useState<number[]>([]);

    // 🔁 sync external value → internal state
    useEffect(() => {
        setSelected(value ?? []);
    }, [value]);

    const toggle = (id: number) => {
        const updated = selected.includes(id)
            ? selected.filter((x) => x !== id)
            : [...selected, id];

        setSelected(updated);
        onChange(updated);
    };

    return (
        <div className="rounded-lg bg-white p-4">
            <h2 className="mb-3 text-lg font-semibold">📂 Select Categories</h2>

            {data.map((item) => (
                <TreeNode
                    key={item.id}
                    node={item}
                    selected={selected}
                    toggle={toggle}
                />
            ))}
        </div>
    );
}

/* ---------------- Tree Node ---------------- */

function TreeNode({
    node,
    selected,
    toggle,
}: {
    node: RecursiveCategory;
    selected: number[];
    toggle: (id: number) => void;
}) {
    const [open, setOpen] = useState(true);
    const hasChildren = node.children_recursive?.length > 0;
    const id = Number(node.id);

    return (
        <div className="ml-4">
            <div className="flex items-center gap-2 py-1">
                {hasChildren ? (
                    <button
                        type="button"
                        onClick={() => setOpen(!open)}
                        className="flex h-5 w-5 items-center justify-center rounded bg-gray-200 text-xs"
                    >
                        {open ? '−' : '+'}
                    </button>
                ) : (
                    <span className="w-5" />
                )}

                <input
                    type="checkbox"
                    checked={selected.includes(id)}
                    onChange={() => toggle(id)}
                    className="h-4 w-4"
                />

                <span className="text-sm text-gray-800">{node.name}</span>
            </div>

            {open && hasChildren && (
                <div className="ml-4 border-l border-gray-300 pl-2">
                    {node.children_recursive.map((child) => (
                        <TreeNode
                            key={child.id}
                            node={child}
                            selected={selected}
                            toggle={toggle}
                        />
                    ))}
                </div>
            )}
        </div>
    );
}

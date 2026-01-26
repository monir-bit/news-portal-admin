import { Button } from '@/components/ui/button';
import { Trash2 } from 'lucide-react';
import Modal from '@/components/shared/modal';

interface DeleteModalProps {
    open: boolean;
    setOpen: (open: boolean) => void;
    onConfirm: () => void;
    title?: string;
    description?: string;
    loading?: boolean;
}

export default function DeleteModal({
    open,
    setOpen,
    onConfirm,
    title = 'Delete item?',
    description = 'This action cannot be undone. This will permanently delete the item.',
    loading = false,
}: DeleteModalProps) {
    return (
        <Modal open={open} setOpen={setOpen} className="max-w-md">
            <div className="flex flex-col gap-4">
                {/* Content */}
                <div className="space-y-1">
                    <h2 className="text-lg font-semibold">{title}</h2>
                    <p className="text-sm text-muted-foreground">
                        {description}
                    </p>
                </div>

                {/* Actions */}
                <div className="flex justify-end gap-2 pt-4">
                    <Button
                        variant="outline"
                        onClick={() => setOpen(false)}
                        disabled={loading}
                    >
                        Cancel
                    </Button>

                    <Button
                        variant="destructive"
                        onClick={onConfirm}
                        disabled={loading}
                    >
                        {loading ? 'Deleting...' : 'Delete'}
                    </Button>
                </div>
            </div>
        </Modal>
    );
}

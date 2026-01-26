import { Button } from '@/components/ui/button';
import { Dialog, DialogContent } from '@/components/ui/dialog';
import { X } from 'lucide-react';
import * as React from 'react';

interface ModalProps {
    open: boolean;
    setOpen: (open: boolean) => void;
    children: React.ReactNode;
    className?: string;
}

export default function Modal({
    open,
    setOpen,
    children,
}: ModalProps) {
    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogContent>
                {/* Modal Body */}
                {children}
            </DialogContent>
        </Dialog>
    );
}

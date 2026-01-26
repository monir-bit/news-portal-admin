import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';
import { FileType } from '@/types';
import { Upload, X } from 'lucide-react';
import * as React from 'react';

interface FormFileImageProps {
    label?: string;
    error?: string;
    helperText?: string;
    required?: boolean;
    disabled?: boolean;
    id?: string;
    className?: string;
    containerClassName?: string;
    imageUrl?: string | null; // initial preview only
    onChange?: (file: FileType) => void;
    accept?: string;
    maxSize?: number;
}

const FormFileImage: React.FC<FormFileImageProps> = ({
    label,
    error,
    helperText,
    required,
    disabled,
    id,
    className,
    containerClassName,
    imageUrl,
    onChange,
    accept = 'image/*',
    maxSize = 5,
}) => {
    const [preview, setPreview] = React.useState<string | null>(
        imageUrl ?? null,
    );
    const [dragActive, setDragActive] = React.useState(false);
    const inputRef = React.useRef<HTMLInputElement>(null);
    const objectUrlRef = React.useRef<string | null>(null);

    const handleFileChange = (file: FileType) => {
        if (!file || typeof file === 'string') {
            clearPreview();
            onChange?.(null);
            return;
        }

        if (!file.type.startsWith('image/')) {
            alert('Please select an image file');
            return;
        }

        const fileSizeMB = file.size / (1024 * 1024);
        if (fileSizeMB > maxSize) {
            alert(`File size must be less than ${maxSize}MB`);
            return;
        }

        // cleanup old object url
        if (objectUrlRef.current) {
            URL.revokeObjectURL(objectUrlRef.current);
        }

        const objectUrl = URL.createObjectURL(file);
        objectUrlRef.current = objectUrl;
        setPreview(objectUrl);
        onChange?.(file);
    };

    const clearPreview = () => {
        if (objectUrlRef.current) {
            URL.revokeObjectURL(objectUrlRef.current);
            objectUrlRef.current = null;
        }

        if (inputRef.current) {
            inputRef.current.value = '';
        }

        setPreview(null);
    };

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0] || null;
        handleFileChange(file);
    };

    const handleClearImage = () => {
        clearPreview();
        onChange?.(null);
    };

    const handleDrag = (e: React.DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        setDragActive(e.type === 'dragenter' || e.type === 'dragover');
    };

    const handleDrop = (e: React.DragEvent) => {
        e.preventDefault();
        e.stopPropagation();
        setDragActive(false);
        if (disabled) return;

        const file = e.dataTransfer.files?.[0];
        if (file) handleFileChange(file);
    };

    const handleClickUpload = () => {
        if (!disabled) inputRef.current?.click();
    };

    // cleanup on unmount
    React.useEffect(() => {
        return () => {
            if (objectUrlRef.current) {
                URL.revokeObjectURL(objectUrlRef.current);
            }
        };
    }, []);

    return (
        <div className={cn('space-y-1.5', containerClassName)}>
            {label && (
                <Label
                    className={cn(
                        'text-sm font-medium',
                        disabled && 'opacity-60',
                    )}
                >
                    {label}
                    {required && <span className="ml-1 text-red-500">*</span>}
                </Label>
            )}

            <Input
                ref={inputRef}
                type="file"
                id={id}
                disabled={disabled}
                accept={accept}
                className="hidden"
                onChange={handleInputChange}
            />

            {preview ? (
                <div className="relative">
                    <div className="overflow-hidden rounded-lg border bg-muted">
                        <img
                            src={preview}
                            alt="Preview"
                            className="h-48 w-full object-cover"
                        />
                    </div>
                    {!disabled && (
                        <button
                            type="button"
                            onClick={handleClearImage}
                            className="absolute top-2 right-2 rounded-full bg-red-500 p-1.5 text-white"
                        >
                            <X className="h-4 w-4" />
                        </button>
                    )}
                </div>
            ) : (
                <div
                    onClick={handleClickUpload}
                    onDragEnter={handleDrag}
                    onDragLeave={handleDrag}
                    onDragOver={handleDrag}
                    onDrop={handleDrop}
                    className={cn(
                        'flex h-48 cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed',
                        dragActive
                            ? 'border-primary bg-primary/5'
                            : 'border-muted-foreground/25 hover:border-primary/50',
                        className,
                    )}
                >
                    <Upload className="mb-2 h-10 w-10 text-muted-foreground" />
                    <p className="text-sm font-medium">
                        Click to upload or drag and drop
                    </p>
                    <p className="mt-1 text-xs text-muted-foreground">
                        PNG, JPG, GIF up to {maxSize}MB
                    </p>
                </div>
            )}

            {helperText && !error && (
                <p className="text-xs text-muted-foreground">{helperText}</p>
            )}
            {error && <p className="text-xs text-red-500">{error}</p>}
        </div>
    );
};

export default FormFileImage;

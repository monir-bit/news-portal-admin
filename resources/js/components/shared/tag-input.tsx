import { X } from 'lucide-react';
import * as React from 'react';

import { Badge } from '@/components/ui/badge';
import {
    Command,
    CommandEmpty,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';

interface TagInputProps {
    suggestions: string[];
    value: string[];
    onChange: (value: string[]) => void;
    placeholder?: string;
    label?: string;
    error?: string;
    helperText?: string;
    required?: boolean;
    id?: string;
    disabled?: boolean;
    containerClassName?: string;
}

export default function TagInput({
    suggestions,
    value,
    onChange,
    placeholder = 'Type and press Enter',
    label,
    error,
    helperText,
    required,
    id,
    disabled,
    containerClassName,
}: TagInputProps) {
    const [input, setInput] = React.useState('');
    const [open, setOpen] = React.useState(false);
    const containerRef = React.useRef<HTMLDivElement>(null);

    const filtered = React.useMemo(() => {
        const q = input.toLowerCase();
        if (!q) return [];
        return suggestions
            .filter(
                (item) =>
                    item.toLowerCase().includes(q) && !value.includes(item),
            )
            .slice(0, 10);
    }, [input, suggestions, value]);

    // Close dropdown when clicking outside
    React.useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (containerRef.current && !containerRef.current.contains(event.target as Node)) {
                setOpen(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, []);

    const addTag = (tag: string) => {
        const clean = tag.trim();
        if (!clean) return;
        if (value.includes(clean)) return;

        onChange([...value, clean]);
        setInput('');
        setOpen(false);
    };

    const removeTag = (tag: string) => {
        onChange(value.filter((t) => t !== tag));
    };

    return (
        <div className={cn('space-y-1.5', containerClassName)} ref={containerRef}>
            {label && (
                <Label
                    htmlFor={id}
                    className={cn(
                        'text-sm font-medium',
                        disabled && 'opacity-60',
                    )}
                >
                    {label}
                    {required && <span className="ml-1 text-red-500">*</span>}
                </Label>
            )}

            <div className="space-y-2">
                {/* Selected tags */}
                {value.length > 0 && (
                    <div className="flex flex-wrap gap-2">
                        {value.map((tag) => (
                            <Badge key={tag} variant="secondary" className="gap-1">
                                {tag}
                                <button
                                    type="button"
                                    onClick={() => removeTag(tag)}
                                    className="hover:text-destructive"
                                    disabled={disabled}
                                >
                                    <X className="h-3 w-3" />
                                </button>
                            </Badge>
                        ))}
                    </div>
                )}

                {/* Input + Suggestions */}
                <div className="relative">
                    <Input
                        id={id}
                        value={input}
                        placeholder={placeholder}
                        disabled={disabled}
                        className={cn(
                            error &&
                                'border-red-500 focus-visible:ring-red-500',
                        )}
                        onChange={(e) => {
                            setInput(e.target.value);
                            if (e.target.value) {
                                setOpen(true);
                            }
                        }}
                        onFocus={() => {
                            if (input && filtered.length > 0) {
                                setOpen(true);
                            }
                        }}
                        onKeyDown={(e) => {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                addTag(input);
                            } else if (e.key === 'Escape') {
                                setOpen(false);
                            }
                        }}
                    />

                    {open && filtered.length > 0 && !disabled && (
                        <div className="absolute top-full z-50 mt-1 w-full rounded-md border bg-popover p-0 shadow-md">
                            <Command>
                                <CommandList>
                                    <CommandEmpty>No match found</CommandEmpty>
                                    {filtered.map((item) => (
                                        <CommandItem
                                            key={item}
                                            onSelect={() => addTag(item)}
                                        >
                                            {item}
                                        </CommandItem>
                                    ))}
                                </CommandList>
                            </Command>
                        </div>
                    )}
                </div>
            </div>

            {helperText && !error && (
                <p className="text-xs text-muted-foreground">{helperText}</p>
            )}

            {error && <p className="text-xs text-red-500">{error}</p>}
        </div>
    );
}

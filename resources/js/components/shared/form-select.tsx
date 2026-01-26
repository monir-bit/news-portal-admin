import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { cn } from '@/lib/utils';

type Option = {
    label: string;
    value: string | number;
};

interface FormSelectProps {
    label?: string;
    value?: string | number | null;
    options: Option[];
    placeholder?: string;
    error?: string;
    disabled?: boolean;
    onChange: (value: string) => void;
    containerClassName?: string;
}

export default function FormSelect({
    label,
    value,
    options,
    placeholder = 'Select an option',
    error,
    disabled,
    onChange,
    containerClassName,
}: FormSelectProps) {
    return (
        <div className={cn('space-y-1.5', containerClassName)}>
            {label && (
                <Label className={disabled ? 'opacity-60' : ''}>{label}</Label>
            )}

            <Select
                value={value ? String(value) : undefined}
                onValueChange={onChange}
                disabled={disabled}
            >
                <SelectTrigger
                    className={cn(error && 'border-red-500 focus:ring-red-500')}
                >
                    <SelectValue placeholder={placeholder} />
                </SelectTrigger>

                <SelectContent>
                    {options.map((option) => (
                        <SelectItem
                            key={option.value}
                            value={String(option.value)}
                        >
                            {option.label}
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>

            {error && <p className="text-xs text-red-500">{error}</p>}
        </div>
    );
}

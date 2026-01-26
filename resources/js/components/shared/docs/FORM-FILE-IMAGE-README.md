# FormFileImage Component

A React component for image file uploads with preview functionality. Built with TypeScript and follows the same design patterns as `FormInput`.

## Features

- ✅ **File Upload**: Click or drag-and-drop to upload images
- ✅ **Image Preview**: Automatic preview for both local files and URL strings
- ✅ **Validation**: Built-in file type and size validation
- ✅ **Error Handling**: Display validation errors
- ✅ **Accessible**: Proper labels, ARIA attributes, and keyboard navigation
- ✅ **Flexible Input**: Accepts both `File` objects and URL strings
- ✅ **Clear Functionality**: Remove selected images with a clear button
- ✅ **Drag & Drop**: Visual feedback during drag operations

## Installation

The component is located at:
```
resources/js/components/shared/form-file-image.tsx
```

## Usage

### Basic Example

```tsx
import FormFileImage from '@/components/shared/form-file-image';
import { useState } from 'react';

function MyForm() {
    const [image, setImage] = useState<File | null>(null);

    return (
        <FormFileImage
            label="Profile Picture"
            value={image}
            onChange={(file) => setImage(file)}
        />
    );
}
```

### With URL String (Editing Existing Records)

```tsx
import FormFileImage from '@/components/shared/form-file-image';
import { useState } from 'react';

function EditForm() {
    // Start with existing image URL from database
    const [image, setImage] = useState<string | File | null>(
        'https://example.com/existing-image.jpg'
    );

    return (
        <FormFileImage
            label="Update Image"
            value={image}
            onChange={(file) => setImage(file)}
            helperText="Upload a new image or keep the existing one"
        />
    );
}
```

### With Form Validation

```tsx
import FormFileImage from '@/components/shared/form-file-image';
import { useForm } from '@inertiajs/react';

function CreateNewsForm() {
    const { data, setData, post, errors } = useForm({
        image: '' as string | File | null,
    });

    return (
        <form onSubmit={handleSubmit}>
            <FormFileImage
                label="News Image"
                value={data.image}
                onChange={(file) => setData('image', file)}
                error={errors.image}
                required
                helperText="Upload an image (PNG, JPG, GIF)"
            />
        </form>
    );
}
```

## Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `label` | `string` | - | Label text for the input |
| `value` | `string \| File \| null` | - | Current value (URL string or File object) |
| `onChange` | `(file: File \| null) => void` | - | Callback when file is selected or cleared |
| `error` | `string` | - | Error message to display |
| `helperText` | `string` | - | Helper text shown below the input |
| `required` | `boolean` | `false` | Whether the field is required |
| `disabled` | `boolean` | `false` | Whether the input is disabled |
| `accept` | `string` | `'image/*'` | Accepted file types |
| `maxSize` | `number` | `5` | Maximum file size in MB |
| `className` | `string` | - | Additional CSS classes for the upload area |
| `containerClassName` | `string` | - | Additional CSS classes for the container |
| `id` | `string` | - | HTML id attribute |

## Features in Detail

### Image Preview

The component automatically shows a preview of the image in three scenarios:

1. **Local File Selection**: When a user selects a file from their computer
2. **URL String**: When the value is a URL string (e.g., from a database)
3. **Dynamic Updates**: When the value prop changes programmatically

### Drag and Drop

Users can drag an image file onto the upload area. The component provides visual feedback:
- Border color changes when dragging over
- Background color highlights the drop zone
- Prevents default browser behavior

### Validation

Built-in validation includes:
- **File Type**: Only allows image files
- **File Size**: Configurable maximum size (default 5MB)
- **User Feedback**: Shows alerts for validation failures

### Clear Functionality

When an image is selected or previewed:
- A clear button (X) appears in the top-right corner
- Clicking it removes the image and resets the value
- The button is hidden when the input is disabled

## Integration with Inertia.js

The component works seamlessly with Inertia.js forms:

```tsx
import { useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';

export default function CreateNews() {
    const { data, setData, post, errors } = useForm({
        title: '',
        image: '' as string | File | null,
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post(route('news.store'));
    }

    return (
        <form onSubmit={submit}>
            <FormFileImage
                label="News Image"
                value={data.image}
                error={errors.image}
                onChange={(file) => setData('image', file)}
                helperText="Upload an image or drag and drop"
            />
            <button type="submit">Submit</button>
        </form>
    );
}
```

## Styling

The component uses Tailwind CSS and shadcn/ui components. It matches the design of other form components in your application.

Key style features:
- Responsive design
- Dark mode support (via `bg-muted`, `text-muted-foreground`)
- Hover and focus states
- Smooth transitions
- Consistent spacing with other form components

## Browser Support

- Modern browsers (Chrome, Firefox, Safari, Edge)
- File API support required
- Drag and Drop API support required

## Dependencies

- React
- lucide-react (for icons: `Upload`, `X`)
- @/components/ui/input
- @/components/ui/label
- @/lib/utils (for `cn` utility)

## Examples

See `form-file-image-demo.tsx` for comprehensive usage examples including:
- Local file selection
- URL string preview
- Mixed usage (File or URL)
- Error states
- Disabled states


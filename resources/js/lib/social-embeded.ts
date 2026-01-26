/**
 * Convert a YouTube URL into an embeddable URL
 *
 * @param url - Any valid YouTube URL
 * @param options - Optional embed parameters
 * @returns YouTube embed URL or null if invalid
 */
export function getYoutubeEmbedUrl(
    url: string,
    options?: {
        autoplay?: boolean;
        mute?: boolean;
        start?: number; // seconds
        loop?: boolean;
    },
): string | null {
    if (!url) return null;

    const regex =
        /(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;

    const match = url.match(regex);
    if (!match || !match[1]) return null;

    const videoId = match[1];

    const params = new URLSearchParams();

    if (options?.autoplay) params.set('autoplay', '1');
    if (options?.mute) params.set('mute', '1');
    if (options?.start !== undefined)
        params.set('start', options.start.toString());
    if (options?.loop) {
        params.set('loop', '1');
        params.set('playlist', videoId); // required by YouTube for looping
    }

    const query = params.toString();
    return `https://www.youtube.com/embed/${videoId}${query ? `?${query}` : ''}`;
}


/**
 * Convert a Facebook post/video URL into an embeddable URL
 *
 * @param url - Any valid Facebook post/video URL
 * @param options - Optional embed parameters
 * @returns Facebook embed URL or null if invalid
 */
export function getFacebookEmbedUrl(
    url: string,
    options?: {
        width?: number;
        showText?: boolean;
        autoplay?: boolean;
    }
): string | null {
    if (!url) return null;

    // Basic validation for facebook domains
    const isFacebook =
        /^(https?:\/\/)?(www\.)?(facebook\.com|fb\.watch)\//i.test(url);

    if (!isFacebook) return null;

    const params = new URLSearchParams();

    // Facebook embed always expects the original URL encoded
    params.set("href", url);

    if (options?.width) params.set("width", options.width.toString());
    if (options?.showText !== undefined)
        params.set("show_text", options.showText ? "true" : "false");
    if (options?.autoplay) params.set("autoplay", "true");

    return `https://www.facebook.com/plugins/post.php?${params.toString()}`;
}


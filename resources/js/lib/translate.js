export const translate = (translations, key, replacements = {}) => {
    if (!translations || !key) {
        return key ?? '';
    }

    const value = key.split('.').reduce((result, segment) => {
        if (result && typeof result === 'object' && segment in result) {
            return result[segment];
        }

        return null;
    }, translations);

    if (typeof value !== 'string') {
        return key;
    }

    return Object.entries(replacements).reduce((result, [replacementKey, replacementValue]) => {
        return result.replaceAll(`:${replacementKey}`, String(replacementValue));
    }, value);
};

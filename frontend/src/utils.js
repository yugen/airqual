/**
 * Normalizes the case of a string to lower, space, cased
 * @param {string} string 
 */
export const normalizeCase = str => {
    if (typeof str !== 'string') {
        console.error('expected string, got '+(typeof str)+' in normalize case.')
        return str;
    }
    const val = str.replace(/[A-Z]/g, letter => ` ${letter.toLowerCase()}`)
            .split(/[\s_-]/)
            .filter(w => w !== ' ' && w !== '')
            .map(w => w.trim())
            .join(' ')
            .trim();
    return val;
}

export const titleCase = string => {
    const parts = normalizeCase(string).split(' ');
    return parts.map(str => str.charAt(0).toUpperCase()+str.slice(1)).join(' ');
}

export const kebabCase = string => {
    return normalizeCase(string).split(' ').join('-');
}

export const snakeCase = string => {
    return normalizeCase(string).split(' ').join('_');
}

export const camelCase = string => {
    const parts = normalizeCase(string).split(' ');
    return parts.map(str => str.charAt(0).toUpperCase()+str.slice(1)).join('');
}

export const sentenceCase = str => {
    if (!str) {
        console.error('expected string, got '+(typeof str)+' in sentence case.')
        return str
    }
    return str.charAt(0).toUpperCase()+str.slice(1);
}

export const arrayContains = (needle, haystack) => {
    if (!haystack) {
        return false;
    }
    if (typeof needle == 'string') {
        return haystack
            .map(i => i.name)
            .includes(needle);
    }
    if (typeof needle == 'object') {
        return haystack
            .map(i => i.id)
            .includes(needle.id);
    }
    return haystack
        .map(i => i.id)
        .includes(needle);
}

export default {
    arrayContains,
    normalizeCase,
    titleCase,
    kebabCase,
    camelCase,
    snakeCase
}
// utils/mediaUtils.js
export function getFileIconClass(mimeType) {
    const iconMap = {
        'image/': 'fas fa-file-image',
        'pdf': 'fas fa-file-pdf',
        'word': 'fas fa-file-word'
    }

    for (const [type, icon] of Object.entries(iconMap)) {
        if (mimeType.includes(type)) return icon
    }
    
    return 'fas fa-file'
}

export function getFileColorClass(mimeType) {
    const colorMap = {
        'image/': 'text-blue-500',
        'pdf': 'text-red-500',
        'word': 'text-indigo-500'
    }

    for (const [type, color] of Object.entries(colorMap)) {
        if (mimeType.includes(type)) return color
    }
    
    return 'text-gray-500'
}

export function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes'
    
    const UNIT = 1024
    const SIZES = ['Bytes', 'KB', 'MB', 'GB']
    const index = Math.floor(Math.log(bytes) / Math.log(UNIT))
    const size = (bytes / Math.pow(UNIT, index)).toFixed(2)
    
    return `${parseFloat(size)} ${SIZES[index]}`
}
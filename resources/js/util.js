/**
 * get cookie value
 * @param {String} searchKey
 * @returns {string}
 */
export function getCookieValue(searchKey){
    if(typeof serchKey === 'undefined'){
        return ''
    }

    let val = ''

    document.cookie.split(';').forEach(cookie => {
        const [key, value] = cookie.split('=')
        if(key === searchKey){
            return val = value
        }
    })

    return val
}
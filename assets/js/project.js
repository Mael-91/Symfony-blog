/**
 * Configuration pour le projet
 * Permet de définir des fonctions utilisées en php
 */


/**
 * @param {String} cloudName
 * @param {String} projectName
 * @param {String} folder
 * @param {String} avatar
 */
export function cloudinaryAvatar(cloudName, projectName, folder, avatar) {
    if (avatar === undefined) {
        return '/test/test_banner.jpg';
    } else {
        return `https://res.cloudinary.com/${cloudName}/image/upload/${projectName}/${folder}/${avatar}`;
    }
}
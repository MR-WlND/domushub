const https = require('https');
const fs = require('fs');
const path = require('path');

const modelsDir = path.join(__dirname, 'public', 'models');
if (!fs.existsSync(modelsDir)) {
    fs.mkdirSync(modelsDir, { recursive: true });
}

const baseUrl = 'https://raw.githubusercontent.com/justadudewhohacks/face-api.js/master/weights/';
const files = [
    'ssd_mobilenetv1_model-weights_manifest.json',
    'ssd_mobilenetv1_model-shard1',
    'ssd_mobilenetv1_model-shard2',
    'face_landmark_68_model-weights_manifest.json',
    'face_landmark_68_model-shard1',
    'face_recognition_model-weights_manifest.json',
    'face_recognition_model-shard1',
    'face_recognition_model-shard2'
];

const downloadFile = (file) => {
    return new Promise((resolve, reject) => {
        const dest = path.join(modelsDir, file);
        if (fs.existsSync(dest) && fs.statSync(dest).size > 0) {
            console.log(`[SKIP] ${file} already exists.`);
            return resolve();
        }
        const fileStream = fs.createWriteStream(dest);
        console.log(`[DOWNLOADING] ${file}...`);
        https.get(baseUrl + file, (response) => {
            if (response.statusCode === 302 || response.statusCode === 301) {
                https.get(response.headers.location, (redirectResponse) => {
                    redirectResponse.pipe(fileStream);
                    fileStream.on('finish', () => {
                        fileStream.close();
                        console.log(`[DONE] ${file}`);
                        resolve();
                    });
                });
            } else {
                response.pipe(fileStream);
                fileStream.on('finish', () => {
                    fileStream.close();
                    console.log(`[DONE] ${file}`);
                    resolve();
                });
            }
        }).on('error', (err) => {
            fs.unlink(dest, () => {});
            reject(err);
        });
    });
};

async function main() {
    for (const file of files) {
        try {
            await downloadFile(file);
        } catch (e) {
            console.error(`Error downloading ${file}:`, e.message);
        }
    }
    console.log('All face-api models process completed.');
}

main();

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { fas } from '@fortawesome/free-solid-svg-icons';

// ES Module 沒有 __dirname，需要這樣寫
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const iconNames = Object.keys(fas).filter(key => key.startsWith('fa')).map(key => {
  // FontAwesome 圖示物件名稱是 faTachometerAlt，轉成 class 名稱格式
  // class 例如 "fas fa-tachometer-alt"
  // 移除前面 "fa" 後轉成 kebab-case
  const iconName = key.slice(2);
  const kebabName = iconName.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
  return `fas fa-${kebabName}`;
});

const outputFile = path.join(__dirname, 'iconList.json');

fs.writeFileSync(outputFile, JSON.stringify(iconNames, null, 2), 'utf-8');

console.log(`產生 ${iconNames.length} 個 FontAwesome 圖示 class 至 ${outputFile}`);

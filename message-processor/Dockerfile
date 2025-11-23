FROM node:20-alpine

WORKDIR /app

COPY package.json package-lock.json* tsconfig.json ./
RUN npm install || yarn install || pnpm install

COPY src ./src

RUN npm run build || yarn build || pnpm build

CMD ["npm", "start"]
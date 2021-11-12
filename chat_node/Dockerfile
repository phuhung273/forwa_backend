FROM node:14-alpine

ENV NODE_ENV=production

# Create app directory
WORKDIR /app

COPY package*.json ./

RUN npm install --production

COPY . .

CMD [ "node", "index" ]
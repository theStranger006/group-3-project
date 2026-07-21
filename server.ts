import express from "express";
import path from "path";
import dotenv from "dotenv";
import { GoogleGenAI } from "@google/genai";
import { createServer as createViteServer } from "vite";

dotenv.config();

const app = express();
const PORT = 3000;

app.use(express.json());

let aiClient: GoogleGenAI | null = null;
function getGeminiClient() {
  if (!aiClient) {
    const key = process.env.GEMINI_API_KEY;
    if (!key) {
      throw new Error("GEMINI_API_KEY is missing. Please add your Gemini API Key in Settings > Secrets to enable the AI Chatbot!");
    }
    aiClient = new GoogleGenAI({
      apiKey: key,
      httpOptions: {
        headers: {
          'User-Agent': 'aistudio-build',
        }
      }
    });
  }
  return aiClient;
}

// AI Chat endpoint
app.post("/api/chat", async (req, res) => {
  try {
    const { messages, products } = req.body;
    
    if (!messages || !Array.isArray(messages)) {
      return res.status(400).json({ error: "Invalid messages array." });
    }

    const client = getGeminiClient();

    // Prepare a grounded context of all products
    let productContext = "Currently, there are no active listings on the market.";
    if (products && Array.isArray(products) && products.length > 0) {
      productContext = "Active Marketplace Listings:\n" + products
        .map((p: any) => `- [ID: ${p.id}] "${p.title}" | Price: KSh ${p.price} | Category: ${p.category} | Location: ${p.location} | Condition: ${p.condition} | Seller: ${p.sellerName} | Description: ${p.description}`)
        .join("\n");
    }

    const systemInstruction = `You are "Soko AI Assistant", the official virtual trading assistant and shopping coordinator for St. Paul's Soko (SPU's student marketplace in Limuru).
Your goals:
1. Ground your recommendations on the actual Soko items provided below. When recommending an item, state its title, price (KSh), seller, and location. If there's an exact match, pitch it enthusiastically.
2. Provide general campus trading, safety, and meetup tips. Advise students to meet in public campus hubs (like SPU Quad, Library, Annex Campus, or near the Chapel) during daylight hours.
3. Offer friendly simulated haggling/negotiation tips. Help students draft polite, respectful negotiation messages they can send to sellers in Soko inbox chat.
4. Keep answers highly friendly, witty, and concise. Avoid giant walls of text. Use clear bullet points and bold headers.

${productContext}`;

    // Transform messages to the format expected by the @google/genai SDK
    // The SDK chat or generateContent takes contents. Let's build the prompt context
    const latestMessage = messages[messages.length - 1];
    const previousConversation = messages
      .slice(0, -1)
      .map((m: any) => `${m.role === "user" ? "Student" : "Soko AI"}: ${m.content}`)
      .join("\n");

    const prompt = previousConversation 
      ? `Here is our conversation history:\n${previousConversation}\n\nLatest student message: "${latestMessage.content}"\n\nPlease reply directly to their latest message.`
      : latestMessage.content;

    const response = await client.models.generateContent({
      model: "gemini-3.5-flash",
      contents: prompt,
      config: {
        systemInstruction: systemInstruction,
        temperature: 0.7,
      },
    });

    const reply = response.text || "I'm sorry, I couldn't process that. Please ask again!";
    res.json({ content: reply });
  } catch (error: any) {
    console.error("Gemini API Error in /api/chat:", error);
    res.status(500).json({ 
      error: error.message || "An error occurred while calling the AI model.",
      isKeyMissing: !process.env.GEMINI_API_KEY
    });
  }
});

// Mount Vite middleware or static files
async function setupVite() {
  if (process.env.NODE_ENV !== "production") {
    const vite = await createViteServer({
      server: { middlewareMode: true },
      appType: "spa",
    });
    app.use(vite.middlewares);
  } else {
    const distPath = path.join(process.cwd(), "dist");
    app.use(express.static(distPath));
    app.get("*", (req, res) => {
      res.sendFile(path.join(distPath, "index.html"));
    });
  }

  app.listen(PORT, "0.0.0.0", () => {
    console.log(`Server running on port ${PORT}`);
  });
}

setupVite();

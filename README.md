# 🏪 St. Paul's Soko (SPU Student Marketplace)

**St. Paul's Soko** is a highly polished, responsive, and secure peer-to-peer student marketplace custom-built for **St. Paul's University (SPU) - Limuru Campus**. Styled with the intuitive visual hierarchy of major e-commerce platforms like Jumia, it provides SPU students with a seamless avenue to browse listings, initiate secure trades, coordinate safe meetups on campus, and run immersive augmented reality preview sessions.

The application leverages **React 18**, **Vite**, and **Tailwind CSS** to deliver desktop-first precision with fully adaptive layouts on any display size. Professional, non-blocking notification alerts are fully powered by **React Toastify**.

---

## 🎨 Core Design Concept & Theme

St. Paul's Soko is styled around an elegant, professional visual identity reflecting SPU colors:
*   **Aesthetics**: Spacious off-whites paired with deep, elegant navy and slate-charcoal accents. Includes full dark-mode responsiveness.
*   **Typography**: Clean, tech-forward fonts with responsive display headings and strict layout alignments.
*   **Negative Space**: Highly deliberate micro-spacing and rhythm that removes grid clutter and promotes clear browsing.

---

## 🚀 Key Modules & Features

### 1. 🛒 SPU Student Catalog (Buyer Dashboard)
*   **Advanced Discovery**: Filter items instantly by category, specific campus location (e.g., Student Union, Science Complex, Library Gate, SPU Chapel), price boundaries, and item condition.
*   **Smart Compare Engine**: Select and view up to two products side-by-side to compare features, prices, and seller ratings.
*   **Quick Actions**: Instantly add items to your personal wishlist or start a direct local chat with the student seller.

### 2. 🎒 Campus Merchant Portal (Seller Dashboard)
*   **Listing Generator**: Easily launch listings with customized titles, category badges, pricing, current stock, item conditions, and descriptive images.
*   **Live Metrics**: Track active revenue, completed sales, currently listed items, and pending meetups.
*   **Transaction Finalization**: Confirm handovers securely using a structured verification status flow.

### 3. 💬 SPU Student Trade Inbox (Message Center)
*   **Local Secure Chat**: Initiate real-time negotiations without sharing personal phone numbers or off-platform payment details.
*   **Negotiation Prompts**: Use contextual quick-reply suggestion chips (e.g., *"Is the price negotiable?"*, *"Can we meet at the Student Union tomorrow?"*) to streamline meetups.
*   **Escrow Access**: Launch the secure Checkout portal directly from the chat conversation context.

### 4. 📳 M-Pesa STK Escrow (Checkout Modal)
*   **Simulated STK Push**: Enter your M-Pesa details and complete checkout using a interactive PIN simulation.
*   **Campus Escrow Protection**: Soko holds the transaction state secure until the physical meetup is confirmed by both buyer and seller.

### 5. 📸 Immersive Soko AR Studio
*   **AR Backdrop Simulator**: Uses the device camera (or high-fidelity SPU Campus fallback backdrops) to project products inside a 3D canvas.
*   **Precise Interaction**: Rotate, zoom, and reposition products using fluid visual controls.
*   **Snapshot Capture**: Save localized product mockups directly to your simulated SPU student gallery.

### 6. 🤖 SPU AI Soko Companion (AI Chatbot)
*   **Smart Shopping Assistant**: Interactive Gemini-powered campus trading chatbot that helps you search the catalog, estimate fair product pricing, and provides safety tips for campus transactions.

### 7. 🛡️ Campus Administrative Center (Admin Dashboard)
*   **KPI Tracking**: Monitor total marketplace revenue, total active users, flagged items, and successful escrow distributions.
*   **Safety Enforcement**: Resolve reported listings, suspend accounts that violate safety guidelines, and view live system event logs.

---

## 📱 Responsive & Adaptive Architecture

The application is rigorously optimized for all viewport classes using fluid responsive containers (`sm:`, `md:`, `lg:`, `xl:`):
*   **Ultra-wide & Desktops**: Features elegant bento-grid expansions and persistent split layouts.
*   **Tablets & Mobile**: Navigation bars collapse into elegant touch-friendly panels, modals expand smoothly to full-width drawer views, and touch targets meet the **44px** standard for responsive convenience.
*   **Grid Scaling**: Grids transition seamlessly from `grid-cols-1` on phone displays up to `grid-cols-3` and `grid-cols-4` on desktop screens.

---

## 🔔 Professional Toast Notification System

Non-blocking, beautiful notification alerts are managed globally using **React Toastify**. Alerts automatically respect the active system theme:
*   🟢 **Success toasts**: Used for completed purchases, listed products, saved profile details, and validated checkouts.
*   🟡 **Warning toasts**: Triggered during invalid entries, missing fields, or permission restrictions.
*   🔴 **Error toasts**: Visible during incorrect password prompts, locked accounts, and network timeouts.

---

## 🛠️ Project Structure

```bash
/
├── server.ts               # Custom Express + Vite backend server
├── package.json            # Application scripts & dependencies
├── metadata.json           # Applet permissions & capabilities
├── src/
│   ├── App.tsx             # Global state controller & primary layout router
│   ├── index.css           # Global Tailwind CSS imports & theme overrides
│   ├── main.tsx            # React application entry point
│   ├── types.ts            # Centralized TypeScript definitions & schemas
│   ├── components/         # Modular user interface components
│   │   ├── CheckoutModal.tsx       # M-Pesa simulated STK Checkout
│   │   ├── DashboardAdmin.tsx      # Administrative control board
│   │   ├── DashboardBuyer.tsx      # SPU student marketplace catalog
│   │   ├── DashboardSeller.tsx     # Student listings & sales manager
│   │   ├── MessageCenter.tsx       # Student trade inbox chat
│   │   ├── ProductCard.tsx         # Catalog reusable card component
│   │   ├── ProductDetailsModal.tsx # Full product description & location map pins
│   │   ├── ProfilePage.tsx         # Bio settings & user transaction histories
│   │   ├── SokoAIChatbot.tsx       # Gemini SPU marketplace guide
│   │   └── SokoARStudio.tsx        # Immersive product placement scene
│   └── data/
│       └── mockData.ts     # Rich sample dataset of products, users, and chats
```

---

## ⚡ Setup & Local Development

Follow these simple steps to spin up St. Paul's Soko locally:

### 1. Install Dependencies
```bash
npm install
```

### 2. Launch Development Server
```bash
npm run dev
```
*The dev server will boot using `tsx` and bind to `http://localhost:3000`.*

### 3. Build & Package for Production
To bundle both the client SPA assets and compile the background Express server into a standalone bundled Node module:
```bash
npm run build
```

---

## 🔐 Safety Guidelines for SPU Students
1.  **Always Meet on Campus**: Keep Soko transactions restricted to well-lit public campus locations (e.g., SPU Library, Science Complex, or Chapel).
2.  **Verify Before Confirming**: Do not trigger the "Confirm Handover" action in your Seller Dashboard until the buyer has inspected and accepted the physical item.
3.  **Utilize Soko Escrow**: Avoid direct off-platform transactions. Use the simulated escrow payments to protect your student funds.

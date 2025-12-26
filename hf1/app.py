import os
from openai import OpenAI
from dotenv import load_dotenv
from flask import Flask, request, render_template, jsonify

app = Flask(__name__)
load_dotenv()

@app.route("/", methods=["GET", "POST"])
def check():
    msg = request.form.get("message") or request.args.get("message")

    if msg:
        response = generate_response(msg)
        return render_template("index.html", response=response)

    return render_template("index.html")

def generate_response(prompt):
    client = OpenAI(
        base_url="https://router.huggingface.co/v1",
        api_key=os.environ["HF_TOKEN"],
    )

    completion = client.chat.completions.create(
        model="moonshotai/Kimi-K2-Instruct-0905",
        messages=[{"role": "user", "content": prompt}],
    )
    print(jsonify(completion.choices[0].message.content));
    return completion.choices[0].message.content

if __name__ == "__main__":
    app.run(debug=True)

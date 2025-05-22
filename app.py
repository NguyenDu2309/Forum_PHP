from transformers import AutoTokenizer, AutoModelForSeq2SeqLM
from flask import Flask, request, jsonify

app = Flask(__name__)

# ✅ Load model and tokenizer once
model_name = "ViHateT5-base-HSD"
tokenizer = AutoTokenizer.from_pretrained(model_name)
model = AutoModelForSeq2SeqLM.from_pretrained(model_name)

@app.route('/analyze', methods=['POST'])
def analyze():
    data = request.get_json()
    input_text = data.get('text')
    prefix = data.get('prefix')

    input_ids = tokenizer(f"{prefix}: {input_text}", return_tensors="pt").input_ids
    output_ids = model.generate(input_ids, max_length=256)
    output_text = tokenizer.decode(output_ids[0], skip_special_tokens=True)

    return jsonify({'result': output_text})

# if __name__ == "__main__":
#     app.run(host="0.0.0.0", port=5000)
#gunicorn -w 1 -b 127.0.0.1:5000 app:app
if __name__ == "__main__":
    from waitress import serve
    serve(app, host="127.0.0.1", port=5000)
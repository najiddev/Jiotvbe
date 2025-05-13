// pages/api/live-play.js
export default async function handler(req, res) {
  const { id } = req.query;
  const user_ip = req.headers["x-forwarded-for"] || req.socket.remoteAddress;
  const portal = "jiotv.be";
  const mac = "00:1A:79:25:5B:B4";
  const deviceid = "75AEBED8B8AAFBDB0B82529522413FA773DC3CF7F975C833FF296702CA5EAC18";
  const serial = "FFDE8EE8C4D1A";

  const headers = {
    "Cookie": `mac=${mac}; stb_lang=en; timezone=GMT`,
    "X-Forwarded-For": user_ip,
    "Referer": `http://${portal}/stalker_portal/c/`,
    "User-Agent": "Mozilla/5.0 (QtEmbedded; U; Linux; C) AppleWebKit/533.3 (KHTML, like Gecko) MAG200 stbapp ver: 2 rev: 250 Safari/533.3",
    "X-User-Agent": "Model: MAG250; Link:"
  };

  try {
    const handshake = await fetch(`http://${portal}/stalker_portal/server/load.php?type=stb&action=handshake&prehash=false&JsHttpRequest=1-xml`, {
      headers
    });
    const handshakeJson = await handshake.json();
    const token = handshakeJson?.js?.random;

    await fetch(`http://${portal}/stalker_portal/server/load.php?type=stb&action=get_profile&device_id=${deviceid}&device_id2=${deviceid}&sn=${serial}&metrics={"mac":"${mac}","random":"${token}"}&JsHttpRequest=1-xml`, {
      headers: {
        ...headers,
        "Authorization": "Bearer EC5897889BE161532C08D180A24B3707"
      }
    });

    const createLink = await fetch(`http://${portal}/stalker_portal/server/load.php?type=itv&action=create_link&cmd=ffrt%http://localhost/ch/${id}&JsHttpRequest=1-xml`, {
      headers: {
        ...headers,
        "Authorization": "Bearer EC5897889BE161532C08D180A24B3707"
      }
    });

    const json = await createLink.json();
    const streamUrl = json?.js?.cmd;

    if (streamUrl) {
      res.writeHead(302, { Location: streamUrl });
      res.end();
    } else {
      res.status(404).json({ error: "Stream link not found." });
    }
  } catch (err) {
    res.status(500).json({ error: "Internal Server Error", details: err.message });
  }
}
